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
 * Data converter
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Payment\Method;

class Converter
{
    /**
     * List of fields that has to be encrypted
     * Format: method_name => array(field1, field2, ... )
     *
     * @var array
     */
    protected $_encryptFields = array(
        'ccsave' => array(
            'cc_owner' => true,
            'cc_exp_year' => true,
            'cc_exp_month' => true,
        ),
    );

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_encryptor;

    public function __construct(
        \Magento\Core\Helper\Data $coreData
    ) {
        $this->_encryptor = $coreData;
    }

    /**
     * Check if specified field is encrypted
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $filedName
     * @return bool
     */
    protected function _shouldBeEncrypted(\Magento\Core\Model\AbstractModel $object, $filedName)
    {
        $method = $object->getData('method');
        return isset($this->_encryptFields[$method][$filedName]) &&
            $this->_encryptFields[$method][$filedName];
    }


    /**
     * Decode data
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function decode(\Magento\Core\Model\AbstractModel $object, $filedName)
    {
        $value = $object->getData($filedName);

        if ($this->_shouldBeEncrypted($object, $filedName)) {
            $value = $this->_encryptor->decrypt($value);
        }

        return $value;
    }

    /**
     * Encode data
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function encode(\Magento\Core\Model\AbstractModel $object, $filedName)
    {
        $value = $object->getData($filedName);

        if ($this->_shouldBeEncrypted($object, $filedName)) {
            $value = $this->_encryptor->encrypt($value);
        }

        return $value;
    }
}
