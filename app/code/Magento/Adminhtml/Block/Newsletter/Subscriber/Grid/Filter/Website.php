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
 * Adminhtml newsletter subscribers grid website filter
 */
namespace Magento\Adminhtml\Block\Newsletter\Subscriber\Grid\Filter;

class Website
    extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    protected $_websiteCollection = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\Resource\Website\CollectionFactory
     */
    protected $_websitesFactory;

    /**
     * @param \Magento\Core\Model\Resource\Website\CollectionFactory $websitesFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\Resource\Helper $resourceHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Resource\Website\CollectionFactory $websitesFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\Resource\Helper $resourceHelper,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_websitesFactory = $websitesFactory;
        parent::__construct($context, $resourceHelper, $data);
    }

    protected function _getOptions()
    {
        $result = $this->getCollection()->toOptionArray();
        array_unshift($result, array('label'=>null, 'value'=>null));
        return $result;
    }

    /**
     * @return \Magento\Core\Model\Resource\Website\Collection|null
     */
    public function getCollection()
    {
        if (is_null($this->_websiteCollection)) {
            $this->_websiteCollection = $this->_websitesFactory->create()->load();
        }

        $this->_coreRegistry->register('website_collection', $this->_websiteCollection);

        return $this->_websiteCollection;
    }

    public function getCondition()
    {
        $id = $this->getValue();
        if (!$id) {
            return null;
        }

        $website = $this->_storeManager->getWebsite($id);
        return array('in' => $website->getStoresIds(true));
    }
}
