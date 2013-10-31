<?php
/**
 * Default application router
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

use \Magento\App\ActionFactory,
    \Magento\App\RequestInterface;

class DefaultRouter extends AbstractRouter
{
    /**
     * @var \Magento\App\Router\NoRouteHandlerList
     */
    protected $_noRouteHandlerList;

    /**
     * @param ActionFactory $controllerFactory
     * @param \Magento\App\Router\NoRouteHandlerList $noRouteHandlerList
     */
    public function __construct(
        ActionFactory $controllerFactory,
        \Magento\App\Router\NoRouteHandlerList $noRouteHandlerList
    ) {
        parent::__construct($controllerFactory);
        $this->_noRouteHandlerList = $noRouteHandlerList;
    }

    /**
     * Modify request and set to no-route action
     *
     * @param RequestInterface $request
     * @return boolean
     */
    public function match(RequestInterface $request)
    {
        foreach ($this->_noRouteHandlerList->getHandlers() as $noRouteHandler) {
            if ($noRouteHandler->process($request)) {
                break;
            }
        }

        return $this->_controllerFactory->createController('Magento\App\Action\Forward',
            array('request' => $request)
        );
    }
}
