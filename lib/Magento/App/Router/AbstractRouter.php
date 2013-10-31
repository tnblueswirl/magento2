<?php
/**
 * Abstract application router
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

use \Magento\App\FrontControllerInterface,
    \Magento\App\ActionFactory,
    \Magento\App\RequestInterface;

abstract class AbstractRouter
{
    /**
     * @var \Magento\App\FrontController
     */
    protected $_front;

    /**
     * @var \Magento\App\ActionFactory
     */
    protected $_controllerFactory;

    /**
     * @param \Magento\App\ActionFactory $controllerFactory
     */
    public function __construct(ActionFactory $controllerFactory)
    {
        $this->_controllerFactory = $controllerFactory;
    }

    /**
     * Assign front controller instance
     *
     * @param $front FrontControllerInterface
     * @return AbstractRouter
     */
    public function setFront(FrontControllerInterface $front)
    {
        $this->_front = $front;
        return $this;
    }

    /**
     * Retrieve front controller instance
     *
     * @return FrontControllerInterface
     */
    public function getFront()
    {
        return $this->_front;
    }

    /**
     * Retrieve front name by route
     *
     * @param string $routeId
     * @return string
     */
    public function getFrontNameByRoute($routeId)
    {
        return $routeId;
    }

    /**
     * Retrieve route by module front name
     *
     * @param string $frontName
     * @return string
     */
    public function getRouteByFrontName($frontName)
    {
        return $frontName;
    }

    /**
     * Match controller by request
     *
     * @param RequestInterface $request
     * @return \Magento\App\Action\AbstractAction
     */
    abstract public function match(RequestInterface $request);
}
