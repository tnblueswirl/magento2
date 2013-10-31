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
 * @category   Magento
 * @package    Magento_Convert
 * @copyright  Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Default converter for \Magento\Objects to arrays
 *
 * @category   Magento
 * @package    Magento_Convert
 * @author     Magento Extensibility Team <DL-X-Extensibility-Team@corp.ebay.com>
 */
namespace Magento\Convert;

class Object
{
    /** Constant used to mark cycles in the input array/objects */
    const CYCLE_DETECTED_MARK = '*** CYCLE DETECTED ***';

    /**
     * Convert input data into an array and return the resulting array.
     * The resulting array should not contain any objects.
     *
     * @param mixed $data input data
     * @return array Data converted to an array
     */
    public function convertDataToArray($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $result[$key] = $this->_convertObjectToArray($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Converts a \Magento\Object into an array, including any children objects
     *
     * @param mixed $obj array or object to convert
     * @param array $objects array of object hashes used for cycle detection
     * @return array|string Converted object or CYCLE_DETECTED_MARK
     */
    protected function _convertObjectToArray($obj, &$objects = array())
    {
        $data = array();
        if (is_object($obj)) {
            $hash = spl_object_hash($obj);
            if (!empty($objects[$hash])) {
                return self::CYCLE_DETECTED_MARK;
            }
            $objects[$hash] = true;
            if ($obj instanceof \Magento\Object) {
                $data = $obj->getData();
            } else {
                $data = (array)$obj;
            }
        } else if (is_array($obj)) {
            $data = $obj;
        }

        $result = array();
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $result[$key] = $value;
            } else if (is_array($value)) {
                $result[$key] = $this->_convertObjectToArray($value, $objects);
            } else if ($value instanceof \Magento\Object) {
                $result[$key] = $this->_convertObjectToArray($value, $objects);
            }
        }
        return $result;
    }

}
