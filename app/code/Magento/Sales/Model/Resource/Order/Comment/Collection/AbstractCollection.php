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
 * Flat sales order abstract comments collection, used as parent for: invoice, shipment, creditmemo
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Comment\Collection;

abstract class AbstractCollection
    extends \Magento\Sales\Model\Resource\Collection\AbstractCollection
{
    /**
     * Set filter on comments by their parent item
     *
     * @param \Magento\Core\Model\AbstractModel|int $parent
     * @return \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
     */
    public function setParentFilter($parent)
    {
        if ($parent instanceof \Magento\Core\Model\AbstractModel) {
            $parent = $parent->getId();
        }
        return $this->addFieldToFilter('parent_id', $parent);
    }

    /**
     * Adds filter to get only 'visible on front' comments
     *
     * @param int $flag
     * @return \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
     */
    public function addVisibleOnFrontFilter($flag = 1)
    {
        return $this->addFieldToFilter('is_visible_on_front', $flag);
    }

    /**
     * Set created_at sort order
     *
     * @param string $direction
     * @return \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
     */
    public function setCreatedAtOrder($direction = 'desc')
    {
        return $this->setOrder('created_at', $direction);
    }
}
