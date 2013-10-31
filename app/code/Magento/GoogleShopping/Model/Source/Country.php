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
 * @package     Magento_GoogleShopping
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Content Target country Source
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Source;

class Country implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Config
     *
     * @var \Magento\GoogleShopping\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\GoogleShopping\Model\Config $config
     */
    function __construct(\Magento\GoogleShopping\Model\Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Retrieve option array with allowed countries
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_allowed = $this->_config->getAllowedCountries();
        $result = array();
        foreach ($_allowed as $iso => $info) {
            $result[] = array('value' => $iso, 'label' => $info['name']);
        }
        return $result;
    }
}
