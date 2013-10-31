<?php
/**
 * Test for validation rules implemented by XSD schemas for email templates configuration
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
namespace Magento\Core\Model\Email\Template\Config;

class XsdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test validation rules implemented by XSD schema for individual config files
     *
     * @param string $fixtureXml
     * @param array $expectedErrors
     * @dataProvider individualXmlDataProvider
     */
    public function testIndividualXml($fixtureXml, array $expectedErrors)
    {
        $schemaFile = BP . '/app/code/Magento/Core/etc/email_templates_file.xsd';
        $this->_testXmlAgainstXsd($fixtureXml, $schemaFile, $expectedErrors);
    }

    public function individualXmlDataProvider()
    {
        return array(
            'valid' => array(
                '<config><template id="test" label="Test" file="test.html" type="html"/></config>',
                array()
            ),
            'empty root node' => array(
                '<config/>',
                array("Element 'config': Missing child element(s). Expected is ( template ).")
            ),
            'irrelevant root node' => array(
                '<template id="test" label="Test" file="test.html" type="html"/>',
                array("Element 'template': No matching global declaration available for the validation root.")
            ),
            'invalid node' => array(
                '<config><invalid/></config>',
                array("Element 'invalid': This element is not expected. Expected is ( template ).")
            ),
            'node "template" with value' => array(
                '<config><template id="test" label="Test" file="test.html" type="html">invalid</template></config>',
                array("Element 'template': Character content is not allowed, because the content type is empty.")
            ),
            'node "template" with children' => array(
                '<config><template id="test" label="Test" file="test.html" type="html"><invalid/></template></config>',
                array("Element 'template': Element content is not allowed, because the content type is empty.")
            ),
            'node "template" without attribute "id"' => array(
                '<config><template label="Test" file="test.html" type="html"/></config>',
                array("Element 'template': The attribute 'id' is required but missing.")
            ),
            'node "template" without attribute "label"' => array(
                '<config><template id="test" file="test.html" type="html"/></config>',
                array("Element 'template': The attribute 'label' is required but missing.")
            ),
            'node "template" without attribute "file"' => array(
                '<config><template id="test" label="Test" type="html"/></config>',
                array("Element 'template': The attribute 'file' is required but missing.")
            ),
            'node "template" without attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.html"/></config>',
                array("Element 'template': The attribute 'type' is required but missing.")
            ),
            'node "template" with invalid attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.html" type="invalid"/></config>',
                array(
                    "Element 'template', attribute 'type': "
                        . "[facet 'enumeration'] The value 'invalid' is not an element of the set {'html', 'text'}.",
                    "Element 'template', attribute 'type': "
                        . "'invalid' is not a valid value of the atomic type 'emailTemplateFormatType'.",
                )
            ),
            'node "template" with unknown attribute "module"' => array(
                '<config><template id="test" label="Test" file="test.html" type="html" module="Test_Module"/></config>',
                array("Element 'template', attribute 'module': The attribute 'module' is not allowed.")
            ),
        );
    }

    /**
     * Test validation rules implemented by XSD schema for merged configs
     *
     * @param string $fixtureXml
     * @param array $expectedErrors
     * @dataProvider mergedXmlDataProvider
     */
    public function testMergedXml($fixtureXml, array $expectedErrors)
    {
        $schemaFile = BP . '/app/code/Magento/Core/etc/email_templates.xsd';
        $this->_testXmlAgainstXsd($fixtureXml, $schemaFile, $expectedErrors);
    }

    public function mergedXmlDataProvider()
    {
        return array(
            'valid' => array(
                '<config><template id="test" label="Test" file="test.txt" type="text" module="Module"/></config>',
                array()
            ),
            'empty root node' => array(
                '<config/>',
                array("Element 'config': Missing child element(s). Expected is ( template ).")
            ),
            'irrelevant root node' => array(
                '<template id="test" label="Test" file="test.txt" type="text" module="Module"/>',
                array("Element 'template': No matching global declaration available for the validation root.")
            ),
            'invalid node' => array(
                '<config><invalid/></config>',
                array("Element 'invalid': This element is not expected. Expected is ( template ).")
            ),
            'node "template" with value' => array(
                '<config>
                    <template id="test" label="Test" file="test.txt" type="text" module="Module">invalid</template>
                </config>',
                array("Element 'template': Character content is not allowed, because the content type is empty.")
            ),
            'node "template" with children' => array(
                '<config>
                    <template id="test" label="Test" file="test.txt" type="text" module="Module"><invalid/></template>
                </config>',
                array("Element 'template': Element content is not allowed, because the content type is empty.")
            ),
            'node "template" without attribute "id"' => array(
                '<config><template label="Test" file="test.txt" type="text" module="Module"/></config>',
                array("Element 'template': The attribute 'id' is required but missing.")
            ),
            'node "template" without attribute "label"' => array(
                '<config><template id="test" file="test.txt" type="text" module="Module"/></config>',
                array("Element 'template': The attribute 'label' is required but missing.")
            ),
            'node "template" without attribute "file"' => array(
                '<config><template id="test" label="Test" type="text" module="Module"/></config>',
                array("Element 'template': The attribute 'file' is required but missing.")
            ),
            'node "template" without attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.txt" module="Module"/></config>',
                array("Element 'template': The attribute 'type' is required but missing.")
            ),
            'node "template" with invalid attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.txt" type="invalid" module="Module"/></config>',
                array(
                    "Element 'template', attribute 'type': "
                    . "[facet 'enumeration'] The value 'invalid' is not an element of the set {'html', 'text'}.",
                    "Element 'template', attribute 'type': "
                    . "'invalid' is not a valid value of the atomic type 'emailTemplateFormatType'.",
                )
            ),
            'node "template" without attribute "module"' => array(
                '<config><template id="test" label="Test" file="test.txt" type="text"/></config>',
                array("Element 'template': The attribute 'module' is required but missing.")
            ),
            'node "template" with unknown attribute' => array(
                '<config>
                    <template id="test" label="Test" file="test.txt" type="text" module="Module" unknown="true"/>
                </config>',
                array("Element 'template', attribute 'unknown': The attribute 'unknown' is not allowed.")
            ),
        );
    }

    /**
     * Test that XSD schema validates fixture XML contents producing expected results
     *
     * @param string $fixtureXml
     * @param string $schemaFile
     * @param array $expectedErrors
     */
    protected function _testXmlAgainstXsd($fixtureXml, $schemaFile, array $expectedErrors)
    {
        $dom = new \Magento\Config\Dom($fixtureXml, array(), null, '%message%');
        $actualResult = $dom->validate($schemaFile, $actualErrors);
        $this->assertEquals(empty($expectedErrors), $actualResult);
        $this->assertEquals($expectedErrors, $actualErrors);
    }
}
