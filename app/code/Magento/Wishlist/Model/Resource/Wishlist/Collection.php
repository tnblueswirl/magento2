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
 * @package     Magento_Wishlist
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wislist model collection
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Model\Resource\Wishlist;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Wishlist\Model\Wishlist', 'Magento\Wishlist\Model\Resource\Wishlist');
    }

    /**
     * Filter collection by customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function filterByCustomer(\Magento\Customer\Model\Customer $customer)
    {
        return $this->filterByCustomerId($customer->getId());
    }

    /**
     * Filter collection by customer id
     *
     * @param int $customerId
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function filterByCustomerId($customerId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        return $this;
    }

    /**
     * Filter collection by customer ids
     *
     * @param array $customerIds
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function filterByCustomerIds(array $customerIds)
    {
        $this->addFieldToFilter('customer_id', array('in' => $customerIds));
        return $this;
    }
}
