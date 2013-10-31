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
 * @package     Magento_Sendfriend
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sendfriend Observer
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sendfriend\Model;

class Observer
{
    /**
     * @var \Magento\Sendfriend\Model\SendfriendFactory
     */
    protected $_sendfriendFactory;

    /**
     * @param \Magento\Sendfriend\Model\SendfriendFactory $sendfriendFactory
     */
    public function __construct(
        \Magento\Sendfriend\Model\SendfriendFactory $sendfriendFactory
    ) {
        $this->_sendfriendFactory = $sendfriendFactory;
    }

    /**
     * Register Sendfriend Model in global registry
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Sendfriend\Model\Observer
     */
    public function register(\Magento\Event\Observer $observer)
    {
        $this->_sendfriendFactory->create()->register();
        return $this;
    }
}
