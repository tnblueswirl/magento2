<?php
/**
 * No route handlers retriever
 *
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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\App\Router;

class NoRouteHandlerList
{
    /**
     * No route handlers instances
     *
     * @var array
     */
    protected $_handlers;

    /**
     * @var array
     */
    protected $_handlerList;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param array $handlerClassesList
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        array $handlerClassesList
    ) {
        $this->_handlerList = $handlerClassesList;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get noRoute handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        if (!$this->_handlers) {

            //sorting handlers list
            $sortedHandlersList = array();
            foreach ($this->_handlerList as $handlerInfo) {
                if (isset($handlerInfo['instance']) && isset($handlerInfo['sortOrder'])) {
                    $sortedHandlersList[$handlerInfo['instance']] = $handlerInfo['sortOrder'];
                }
            }

            asort($sortedHandlersList);

            //creating handlers
            foreach (array_keys($sortedHandlersList) as $handlerInstance) {
                $this->_handlers[] = $this->_objectManager->create($handlerInstance);
            }
        }

        return $this->_handlers;
    }
}
