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
 * @package     Magento_Eav
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Validation EAV entity via EAV attributes' backend models
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Validator\Attribute;

class Backend extends \Magento\Validator\AbstractValidator
{
    /**
     * Returns true if and only if $value meets the validation requirements.
     *
     * @param \Magento\Core\Model\AbstractModel $entity
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function isValid($entity)
    {
        $this->_messages = array();
        if (!($entity instanceof \Magento\Core\Model\AbstractModel)) {
            throw new \InvalidArgumentException('Model must be extended from \Magento\Core\Model\AbstractModel');
        }
        /** @var \Magento\Eav\Model\Entity\AbstractEntity $resource */
        $resource = $entity->getResource();
        if (!($resource instanceof \Magento\Eav\Model\Entity\AbstractEntity)) {
            throw new \InvalidArgumentException('Model resource must be extended from \Magento\Eav\Model\Entity\AbstractEntity');
        }
        $resource->loadAllAttributes($entity);
        $attributes = $resource->getAttributesByCode();
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($attributes as $attribute) {
            $backend = $attribute->getBackend();
            if (!method_exists($backend, 'validate')) {
                continue;
            }
            try {
                $result = $backend->validate($entity);
                if (false === $result) {
                    $this->_messages[$attribute->getAttributeCode()][] =
                        __('The value of attribute "%1" is invalid',
                            $attribute->getAttributeCode());
                } elseif (is_string($result)) {
                    $this->_messages[$attribute->getAttributeCode()][] = $result;
                }
            } catch (\Magento\Core\Exception $e) {
                $this->_messages[$attribute->getAttributeCode()][] = $e->getMessage();
            }
        }
        return 0 == count($this->_messages);
    }
}
