<?php
/**
 * Test Rest controller.
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
namespace Magento\Webapi\Controller;

class RestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Rest */
    protected $_restController;

    /** @var \Magento\Webapi\Controller\Rest\Request */
    protected $_requestMock;

    /** @var \Magento\Webapi\Controller\Rest\Response */
    protected $_responseMock;

    /** @var \Magento\Webapi\Controller\Rest\Router */
    protected $_routerMock;

    /** @var \Magento\Webapi\Controller\Rest\Router\Route */
    protected $_routeMock;

    /** @var \Magento\ObjectManager */
    protected $_objectManagerMock;

    /** @var \stdClass */
    protected $_serviceMock;

    /** @var \Magento\App\State */
    protected $_appStateMock;

    /** @var \Magento\Oauth\Service\OauthV1 */
    protected $_oauthServiceMock;

    /** @var \Magento\Oauth\Helper\Data */
    protected $_oauthHelperMock;

    const SERVICE_METHOD = \Magento\Webapi\Model\Rest\Config::KEY_METHOD;
    const SERVICE_ID = \Magento\Webapi\Model\Rest\Config::KEY_CLASS;

    protected function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Request')
            ->setMethods(array('isSecure', 'getRequestData'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_responseMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Response')
            ->setMethods(array('sendResponse', 'getHeaders', 'prepareResponse'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_routerMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Router')
            ->setMethods(array('match'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_routeMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Router\Route')
            ->setMethods(array('isSecure', 'getServiceMethod', 'getServiceClass'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_serviceMock = $this->getMockBuilder('stdClass')
            ->setMethods(array(self::SERVICE_METHOD))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_appStateMock =  $this->getMockBuilder('Magento\App\State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_oauthServiceMock = $this->getMockBuilder('Magento\Oauth\Service\OauthV1')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_oauthHelperMock = $this->getMockBuilder('Magento\Oauth\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        /** Init SUT. */
        $this->_restController = new \Magento\Webapi\Controller\Rest(
            $this->_requestMock,
            $this->_responseMock,
            $this->_routerMock,
            $this->_objectManagerMock,
            $this->_appStateMock,
            $this->_oauthServiceMock,
            $this->_oauthHelperMock
        );

        // Set default expectations used by all tests
        $this->_routeMock
            ->expects($this->any())->method('getServiceClass')->will($this->returnValue(self::SERVICE_ID));

        $this->_routeMock
            ->expects($this->any())->method('getServiceMethod')->will($this->returnValue(self::SERVICE_METHOD));
        $this->_routerMock->expects($this->any())->method('match')->will($this->returnValue($this->_routeMock));

        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValue($this->_serviceMock));
        $this->_responseMock->expects($this->any())->method('prepareResponse')->will($this->returnValue(array()));
        $this->_requestMock->expects($this->any())->method('getRequestData')->will($this->returnValue(array()));

        /** Assert that response sendResponse method will be executed once. */
        $this->_responseMock->expects($this->once())->method('sendResponse');

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restController);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_routerMock);
        unset($this->_objectManagerMock);
        unset($this->_oauthServiceMock);
        unset($this->_oauthHelperMock);
        unset($this->_appStateMock);
        parent::tearDown();
    }

    /**
     * Test redirected to install page
     */
    public function testRedirectToInstallPage()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(false));
        $expectedMsg = 'Magento is not yet installed';

        $this->_restController->dispatch($this->_requestMock);
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
    }

    /**
     * Test dispatch method with \Exception throwing.
     */
    public function testDispatchAuthenticationException()
    {
        $this->markTestIncomplete(
            "Test should be fixed after \Magento\Webapi\Controller\Rest::dispatch() enforces authentication"
        );
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
    }

    /**
     * Test Secure Request and Secure route combinations
     *
     * @dataProvider dataProviderSecureRequestSecureRoute
     */
    public function testSecureRouteAndRequest($isSecureRoute, $isSecureRequest)
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock
            ->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRoute));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRequest));
        $this->_restController->dispatch($this->_requestMock);
        $this->assertFalse($this->_responseMock->isException());
    }

    /**
     * Data provider for testSecureRouteAndRequest.
     *
     * @return array
     */
    public function dataProviderSecureRequestSecureRoute()
    {
        return array(
            //Each array contains return type for isSecure method of route and request objects .
            array(
                true,
                true
            ),
            array(
                false,
                true
            ),
            array(
                false,
                false
            )
        );

    }

    /**
     * Test insecure request for a secure route
     */
    public function testInSecureRequestOverSecureRoute()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));

        // Override default prepareResponse. It should never be called in this case
        $this->_responseMock->expects($this->never())->method('prepareResponse');

        $this->_restController->dispatch($this->_requestMock);
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals('Operation allowed only in HTTPS', $exceptionArray[0]->getMessage());
        $this->assertEquals(\Magento\Webapi\Exception::HTTP_BAD_REQUEST, $exceptionArray[0]->getHttpCode());
    }

    /**
     * Test incorrect format type response from service methods
     */
    public function testInvalidReturnTypeFromService()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue("invalid"));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));

        // Override default prepareResponse. It should never be called in this case
        $this->_responseMock->expects($this->never())->method('prepareResponse');

        $expectedMsg = 'The method "' . self::SERVICE_METHOD . '" of service "'
            . self::SERVICE_ID . '" must return an array.';

        $this->_restController->dispatch($this->_requestMock);
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
    }
}
