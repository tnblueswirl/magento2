<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Magento\Shipping\Model\Carrier;

class Tablerate
    extends \Magento\Shipping\Model\Carrier\AbstractCarrier
    implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    /**
     * @var string
     */
    protected $_code = 'tablerate';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var string
     */
    protected $_default_condition_name = 'package_weight';

    /**
     * @var array
     */
    protected $_conditionNames = array();

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Shipping\Model\Rate\Result\MethodFactory
     */
    protected $_resultMethodFactory;

    /**
     * @var \Magento\Shipping\Model\Resource\Carrier\TablerateFactory
     */
    protected $_tablerateFactory;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Shipping\Model\Rate\Result\ErrorFactory $rateErrorFactory
     * @param \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory
     * @param \Magento\Shipping\Model\Rate\Result\MethodFactory $resultMethodFactory
     * @param \Magento\Shipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Shipping\Model\Rate\Result\ErrorFactory $rateErrorFactory,
        \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Shipping\Model\Rate\Result\MethodFactory $resultMethodFactory,
        \Magento\Shipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory,
        array $data = array()
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_resultMethodFactory = $resultMethodFactory;
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($coreStoreConfig, $rateErrorFactory, $logAdapterFactory, $data);
        foreach ($this->getCode('condition_name') as $k => $v) {
            $this->_conditionNames[] = $k;
        }
    }

    /**
     * @param \Magento\Shipping\Model\Rate\Request $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Shipping\Model\Rate\Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }

        if (!$request->getConditionName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionName($conditionName ? $conditionName : $this->_default_condition_name);
        }

         // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $rate = $this->getRate($request);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        if (!empty($rate) && $rate['price'] >= 0) {
            /** @var \Magento\Shipping\Model\Rate\Result\Method $method */
            $method = $this->_resultMethodFactory->create();

            $method->setCarrier('tablerate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('bestway');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || ($request->getPackageQty() == $freeQty)) {
                $shippingPrice = 0;
            } else {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
            }

            $method->setPrice($shippingPrice);
            $method->setCost($rate['cost']);

            $result->append($method);
        }

        return $result;
    }

    /**
     * @param \Magento\Shipping\Model\Rate\Request $request
     * @return array|bool
     */
    public function getRate(\Magento\Shipping\Model\Rate\Request $request)
    {
        return $this->_tablerateFactory->create()->getRate($request);
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     * @throws \Magento\Shipping\Exception
     */
    public function getCode($type, $code='')
    {
        $codes = array(

            'condition_name'=>array(
                'package_weight' => __('Weight vs. Destination'),
                'package_value'  => __('Price vs. Destination'),
                'package_qty'    => __('# of Items vs. Destination'),
            ),

            'condition_name_short'=>array(
                'package_weight' => __('Weight (and above)'),
                'package_value'  => __('Order Subtotal (and above)'),
                'package_qty'    => __('# of Items (and above)'),
            ),

        );

        if (!isset($codes[$type])) {
            throw new \Magento\Shipping\Exception( __('Please correct Table Rate code type: %1.', $type));
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new \Magento\Shipping\Exception(__('Please correct Table Rate code for type %1: %2.', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('bestway'=>$this->getConfigData('name'));
    }

}
