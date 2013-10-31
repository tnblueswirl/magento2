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
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Test\Integrity\Modular;

class SystemConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = $objectManager->get('Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled(\Magento\Core\Model\Cache\Type\Config::TYPE_IDENTIFIER, false);

        /** @var $dirs \Magento\App\Dir */
        $dirs = $objectManager->get('Magento\App\Dir');
        $modulesDir = $dirs->getDir(\Magento\App\Dir::MODULES);

        $fileList = glob($modulesDir . '/*/*/etc/adminhtml/system.xml');

        $configMock = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array('getConfigurationFiles', 'getModuleDir'),
            array(), '', false
        );
        $configMock->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValue($fileList))
        ;
        $configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Backend')
            ->will($this->returnValue($modulesDir . '/Magento/Backend/etc'))
        ;
        try {
            $objectManager->create('Magento\Backend\Model\Config\Structure\Reader', array(
                'moduleReader' => $configMock,
                'runtimeValidation' => true,
            ));
        } catch (\Magento\Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
