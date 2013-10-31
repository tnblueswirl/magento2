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
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test theme observer
 */
namespace Magento\Core\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Event\Observer
     */
    protected $_eventObserver;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_eventObserver = $this->_createEventObserverForThemeRegistration();
    }

    /**
     * Theme registration test
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testThemeRegistration()
    {
        $baseDir = 'base_dir';
        $pattern = 'path_pattern';

        $this->_eventObserver->getEvent()->setBaseDir($baseDir);
        $this->_eventObserver->getEvent()->setPathPattern($pattern);

        $themeRegistration = $this->getMock(
            'Magento\Core\Model\Theme\Registration',
            array('register'),
            array(
                $this->_objectManager->create('Magento\Core\Model\Resource\Theme\CollectionFactory'),
                $this->_objectManager->create('Magento\Core\Model\Theme\Collection')
            )
        );
        $themeRegistration->expects($this->once())
            ->method('register')
            ->with($baseDir, $pattern);
        $this->_objectManager->addSharedInstance($themeRegistration, 'Magento\Core\Model\Theme\Registration');

        /** @var $observer \Magento\Core\Model\Observer */
        $observer = $this->_objectManager->create('Magento\Core\Model\Observer');
        $observer->themeRegistration($this->_eventObserver);
    }

    /**
     * Create event observer for theme registration
     *
     * @return \Magento\Event\Observer
     */
    protected function _createEventObserverForThemeRegistration()
    {
        $response = $this->_objectManager->create('Magento\Object', array(
            'data' => array('additional_options' => array())
        ));
        $event = $this->_objectManager->create('Magento\Event', array('data' => array('response_object' => $response)));
        return $this->_objectManager->create('Magento\Event\Observer', array('data' => array('event' => $event)));
    }
}
