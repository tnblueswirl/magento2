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
 * @package     Magento_Customer
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer group collection
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Resource\Group;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Customer\Model\Group', 'Magento\Customer\Model\Resource\Group');
    }

    /**
     * Set ignore ID filter
     *
     * @param array $indexes
     * @return \Magento\Customer\Model\Resource\Group\Collection
     */
    public function setIgnoreIdFilter($indexes)
    {
        if (count($indexes)) {
            $this->addFieldToFilter('main_table.customer_group_id', array('nin' => $indexes));
        }
        return $this;
    }

    /**
     * Set real groups filter
     *
     * @return \Magento\Customer\Model\Resource\Group\Collection
     */
    public function setRealGroupsFilter()
    {
        return $this->addFieldToFilter('customer_group_id', array('gt' => 0));
    }

    /**
     * Add tax class
     *
     * @return \Magento\Customer\Model\Resource\Group\Collection
     */
    public function addTaxClass()
    {
        $this->getSelect()->joinLeft(
            array('tax_class_table' => $this->getTable('tax_class')),
            "main_table.tax_class_id = tax_class_table.class_id");
        return $this;
    }

    /**
     * Retreive option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('customer_group_id', 'customer_group_code');
    }

    /**
     * Retreive option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('customer_group_id', 'customer_group_code');
    }
}
