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
 * Sales orders statuses option array
 */
namespace Magento\Sales\Model\Resource\Order\Grid;

class StatusesArray implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Status\CollectionFactory
     */
    protected $_statusCollFactory;

    /**
     * @param \Magento\Sales\Model\Resource\Order\Status\CollectionFactory $statusCollFactory
     */
    public function __construct(\Magento\Sales\Model\Resource\Order\Status\CollectionFactory $statusCollFactory)
    {
        $this->_statusCollFactory = $statusCollFactory;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = $this->_statusCollFactory->create()->toOptionHash();
        return $statuses;
    }
}
