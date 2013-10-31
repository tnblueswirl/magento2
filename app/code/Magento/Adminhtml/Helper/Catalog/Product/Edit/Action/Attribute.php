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
 * @package     Magento_Adminhtml
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product action attribute update helper
 */
namespace Magento\Adminhtml\Helper\Catalog\Product\Edit\Action;

class Attribute extends \Magento\Backend\Helper\Data
{
    /**
     * Selected products for mass-update
     *
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_products;

    /**
     * Array of same attributes for selected products
     *
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    protected $_attributes;

    /**
     * Excluded from batch update attribute codes
     *
     * @var array
     */
    protected $_excludedAttributes = array('url_key');

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productsFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Config\Primary $primaryConfig
     * @param \Magento\App\RouterList $routerList
     * @param \Magento\Core\Model\App\Proxy $app
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\App\Area\FrontNameResolver $frontNameResolver
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productsFactory
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Config\Primary $primaryConfig,
        \Magento\App\RouterList $routerList,
        \Magento\Core\Model\App\Proxy $app,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\App\Area\FrontNameResolver $frontNameResolver,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Backend\Model\Session $session,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productsFactory
    ) {

        $this->_eavConfig = $eavConfig;
        $this->_session = $session;
        $this->_productsFactory = $productsFactory;
        parent::__construct(
            $context, $coreData, $primaryConfig, $routerList, $app, $backendUrl, $auth, $frontNameResolver
        );
    }

    /**
     * Return product collection with selected product filter
     * Product collection didn't load
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getProducts()
    {
        if (is_null($this->_products)) {
            $productsIds = $this->getProductIds();

            if (!is_array($productsIds)) {
                $productsIds = array(0);
            }

            $this->_products = $this->_productsFactory->create()
                ->setStoreId($this->getSelectedStoreId())
                ->addIdFilter($productsIds);
        }

        return $this->_products;
    }

    /**
     * Return array of selected product ids from post or session
     *
     * @return array|null
     */
    public function getProductIds()
    {
        if ($this->_getRequest()->isPost() && $this->_getRequest()->getActionName() == 'edit') {
            $this->_session->setProductIds($this->_getRequest()->getParam('product', null));
        }

        return $this->_session->getProductIds();
    }

    /**
     * Return selected store id from request
     *
     * @return integer
     */
    public function getSelectedStoreId()
    {
        return (int)$this->_getRequest()->getParam('store', \Magento\Core\Model\AppInterface::ADMIN_STORE_ID);
    }

    /**
     * Return array of attribute sets by selected products
     *
     * @return array
     */
    public function getProductsSetIds()
    {
        return $this->getProducts()->getSetIds();
    }

    /**
     * Return collection of same attributes for selected products without unique
     *
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes  = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
                ->getAttributeCollection()
                ->addIsNotUniqueFilter()
                ->setInAllAttributeSetsFilter($this->getProductsSetIds());

            if ($this->_excludedAttributes) {
                $this->_attributes->addFieldToFilter('attribute_code', array('nin' => $this->_excludedAttributes));
            }

            // check product type apply to limitation and remove attributes that impossible to change in mass-update
            $productTypeIds  = $this->getProducts()->getProductTypeIds();
            foreach ($this->_attributes as $attribute) {
                /* @var $attribute \Magento\Catalog\Model\Entity\Attribute */
                foreach ($productTypeIds as $productTypeId) {
                    $applyTo = $attribute->getApplyTo();
                    if (count($applyTo) > 0 && !in_array($productTypeId, $applyTo)) {
                        $this->_attributes->removeItemByKey($attribute->getId());
                        break;
                    }
                }
            }
        }

        return $this->_attributes;
    }
}
