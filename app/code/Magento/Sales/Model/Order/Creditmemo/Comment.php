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
 * @package     Magento_Sales
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @method \Magento\Sales\Model\Resource\Order\Creditmemo\Comment _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Creditmemo\Comment getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setCreatedAt(string $value)
 */
namespace Magento\Sales\Model\Order\Creditmemo;

class Comment extends \Magento\Sales\Model\AbstractModel
{
    /**
     * Creditmemo instance
     *
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    protected $_creditmemo;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\LocaleInterface $coreLocale
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\LocaleInterface $coreLocale,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $coreLocale, $resource, $resourceCollection, $data
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Creditmemo\Comment');
    }

    /**
     * Declare Creditmemo instance
     *
     * @param   \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return  \Magento\Sales\Model\Order\Creditmemo\Comment
     */
    public function setCreditmemo(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Retrieve Creditmemo instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }

    /**
     * Get store object
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if ($this->getCreditmemo()) {
            return $this->getCreditmemo()->getStore();
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Before object save
     *
     * @return \Magento\Sales\Model\Order\Creditmemo\Comment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getCreditmemo()) {
            $this->setParentId($this->getCreditmemo()->getId());
        }

        return $this;
    }
}
