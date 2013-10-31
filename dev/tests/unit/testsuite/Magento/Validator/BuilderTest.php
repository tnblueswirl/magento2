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
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Validator;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * Test createValidator method
     *
     * @dataProvider createValidatorDataProvider
     *
     * @param array $constraints
     * @param \Magento\Validator\ValidatorInterface $expectedValidator
     */
    public function testCreateValidator(array $constraints, $expectedValidator)
    {
        /** @var $builder \Magento\Validator\Builder */
        $builder = $this->_objectManager->getObject(
            'Magento\Validator\Builder',
            array(
                'constraintFactory'
                    => new \Magento\Validator\ConstraintFactory(new \Magento\ObjectManager\ObjectManager()),
                'validatorFactory'
                    => new \Magento\ValidatorFactory(new \Magento\ObjectManager\ObjectManager()),
                'oneValidatorFactory'
                    => new \Magento\Validator\UniversalFactory(new \Magento\ObjectManager\ObjectManager()),
                'constraints' => $constraints
            )
        );
        $actualValidator = $builder->createValidator();
        $this->assertEquals($expectedValidator, $actualValidator);
    }

    /**
     * Data provider for
     *
     * @return array
     */
    public function createValidatorDataProvider()
    {
        $result = array();

        /** @var \Magento\Translate\AbstractAdapter $translator */
        $translator = $this->getMockBuilder('Magento\Translate\AbstractAdapter')
            ->getMockForAbstractClass();
        \Magento\Validator\AbstractValidator::setDefaultTranslator($translator);

        // Case 1. Check constructor with arguments
        $actualConstraints = array(array(
            'alias' => 'name_alias',
            'class' => 'Magento\Validator\Test\StringLength',
            'options' => array(
                'arguments' => array('min' => 1, 'max' => new \Magento\Validator\Constraint\Option(20))
            ),
            'property' => 'name',
            'type' => 'property',
        ));

        $expectedValidator = new \Magento\Validator();
        $expectedValidator->addValidator(
            new \Magento\Validator\Constraint\Property(
                new \Magento\Validator\Test\StringLength(1, 20), 'name', 'name_alias'
            )
        );

        $result[] = array($actualConstraints, $expectedValidator);

        // Case 2. Check method calls
        $actualConstraints = array(array(
            'alias' => 'description_alias',
            'class' => 'Magento\Validator\Test\StringLength',
            'options' => array(
                'methods' => array (
                    array(
                        'method' => 'setMin',
                        'arguments' => array(10)
                    ),
                    array(
                        'method' => 'setMax',
                        'arguments' => array(1000)
                    )
                ),
            ),
            'property' => 'description',
            'type' => 'property',
        ));

        $expectedValidator = new \Magento\Validator();
        $expectedValidator->addValidator(
            new \Magento\Validator\Constraint\Property(
                new \Magento\Validator\Test\StringLength(10, 1000), 'description', 'description_alias'
            )
        );

        $result[] = array($actualConstraints, $expectedValidator);

        // Case 3. Check callback on validator
        $actualConstraints = array(array(
            'alias' => 'sku_alias',
            'class' => 'Magento\Validator\Test\StringLength',
            'options' => array(
                'callback' => array(new \Magento\Validator\Constraint\Option\Callback(
                    function ($validator) {
                        $validator->setMin(20);
                        $validator->setMax(100);
                    }
                ))
            ),
            'property' => 'sku',
            'type' => 'property',
        ));

        $expectedValidator = new \Magento\Validator();
        $expectedValidator->addValidator(
            new \Magento\Validator\Constraint\Property(
                new \Magento\Validator\Test\StringLength(20, 100), 'sku', 'sku_alias'
            )
        );

        $result[] = array($actualConstraints, $expectedValidator);

        return $result;
    }

    /**
     * Check addConfiguration logic
     *
     * @dataProvider configurationDataProvider
     *
     * @param array $constraints
     * @param string $alias
     * @param array $configuration
     * @param array $expected
     */
    public function testAddConfiguration($constraints, $alias, $configuration, $expected)
    {
        /** @var $builder \Magento\Validator\Builder */
        $builder = $this->_objectManager->getObject(
            'Magento\Validator\Builder',
            array('constraints' => $constraints)
        );
        $builder->addConfiguration($alias, $configuration);
        $this->assertAttributeEquals($expected, '_constraints', $builder);
    }

    /**
     * Check addConfigurations logic
     *
     * @dataProvider configurationDataProvider
     *
     * @param array $constraints
     * @param string $alias
     * @param array $configuration
     * @param array $expected
     */
    public function testAddConfigurations($constraints, $alias, $configuration, $expected)
    {
        /** @var $builder \Magento\Validator\Builder */
        $builder = $this->_objectManager->getObject(
            'Magento\Validator\Builder',
            array('constraints' => $constraints)
        );
        $configurations = array($alias => array($configuration));
        $builder->addConfigurations($configurations);
        $this->assertAttributeEquals($expected, '_constraints', $builder);
    }

    /**
     * Builder configurations data provider
     *
     * @return array
     */
    public function configurationDataProvider()
    {
        $callback = new \Magento\Validator\Constraint\Option\Callback(
            array('Magento\Validator\Test\Callback', 'getId')
        );
        $someMethod = array('method' => 'getMessages');
        $methodWithArgs = array('method' => 'setMax', 'arguments' => array(100));
        $constructorArgs = array('arguments' => array(array('max' => '50')));
        $callbackConfig = array('callback' => $callback);

        $configuredConstraint = array(
            'alias' => 'current_alias',
            'class' => 'Magento\Validator\Test\NotEmpty',
            'options' => array(
                'arguments' => array(array('min' => 1)),
                'callback' => array($callback),
                'methods' => array($someMethod)
            ),
            'property' => 'int',
            'type' => 'property'
        );
        $emptyConstraint = array(
            'alias' => 'current_alias',
            'class' => 'Magento\Validator\Test\NotEmpty',
            'options' => null,
            'property' => 'int',
            'type' => 'property'
        );
        $constraintWithArgs = array(
            'alias' => 'current_alias',
            'class' => 'Magento\Validator\Test\NotEmpty',
            'options' => array('arguments' => array(array('min' => 1))),
            'property' => 'int',
            'type' => 'property'
        );
        return array(
            'constraint is unchanged when alias not found' => array(
                array($emptyConstraint), 'some_alias', $someMethod, array($emptyConstraint)),

            'constraint options initialized with method' => array(array($emptyConstraint), 'current_alias', $someMethod,
                array($this->_getExpectedConstraints($emptyConstraint, 'methods', array($someMethod)))),

            'constraint options initialized with callback' => array(array($emptyConstraint), 'current_alias',
                $callbackConfig, array($this->_getExpectedConstraints($emptyConstraint, 'callback', array($callback)))),

            'constraint options initialized with arguments' => array(
                array($emptyConstraint), 'current_alias', $constructorArgs,
                array($this->_getExpectedConstraints($emptyConstraint, 'arguments', array(array('max' => '50'))))
            ),

            'methods initialized' => array(
                array($constraintWithArgs), 'current_alias', $methodWithArgs,
                array($this->_getExpectedConstraints($constraintWithArgs, 'methods', array($methodWithArgs)))
            ),

            'method added' => array(
                array($configuredConstraint), 'current_alias', $methodWithArgs,
                array($this->_getExpectedConstraints($configuredConstraint, 'methods',
                    array($someMethod, $methodWithArgs)))
            ),

            'callback initialized' => array(
                array($constraintWithArgs), 'current_alias', $callbackConfig,
                array($this->_getExpectedConstraints($constraintWithArgs, 'callback', array($callback)))
            ),

            'callback added' => array(
                array($configuredConstraint), 'current_alias', $callbackConfig,
                array($this->_getExpectedConstraints($configuredConstraint, 'callback', array($callback, $callback)))
            ),
        );
    }

    /**
     * Get expected constraint configuration by actual and changes
     *
     * @param array $constraint
     * @param string $optionKey
     * @param mixed $optionValue
     * @return array
     */
    protected function _getExpectedConstraints($constraint, $optionKey, $optionValue)
    {
        if (!is_array($constraint['options'])) {
            $constraint['options'] = array();
        }
        $constraint['options'][$optionKey] = $optionValue;
        return $constraint;
    }

    /**
     * Check arguments validation passed into constructor
     *
     * @dataProvider invalidArgumentsDataProvider
     *
     * @param array $options
     * @param string $exception
     * @param string $exceptionMessage
     */
    public function testConstructorConfigValidation(array $options, $exception, $exceptionMessage)
    {
        $this->setExpectedException($exception, $exceptionMessage);
        if (array_key_exists('method', $options)) {
            $options = array(
                'methods' => array($options)
            );
        }
        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Magento\Validator\Test\True',
            'options' => $options,
            'type' => 'entity'
        ));
        $this->_objectManager->getObject(
            'Magento\Validator\Builder',
            array('constraints' => $constraints)
        );
    }

    /**
     * Check arguments validation passed into configuration
     *
     * @dataProvider invalidArgumentsDataProvider
     *
     * @param array $options
     * @param string $exception
     * @param string $exceptionMessage
     */
    public function testAddConfigurationConfigValidation(array $options, $exception, $exceptionMessage)
    {
        $this->setExpectedException($exception, $exceptionMessage);

        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Magento\Validator\Test\True',
            'options' => null,
            'type' => 'entity'
        ));
        /** @var $builder \Magento\Validator\Builder */
        $builder = $this->_objectManager->getObject(
            'Magento\Validator\Builder',
            array('constraints' => $constraints)
        );
        $builder->addConfiguration('alias', $options);
    }

    /**
     * Data provider for testing configuration validation
     *
     * @return array
     */
    public function invalidArgumentsDataProvider()
    {
        return array(
            'constructor invalid arguments' => array(
                array(
                    'arguments' => 'invalid_argument'
                ),
                'InvalidArgumentException',
                'Arguments must be an array'
            ),

            'methods invalid arguments' => array(
                array(
                    'method' => 'setValue',
                    'arguments' => 'invalid_argument'
                ),
                'InvalidArgumentException',
                'Method arguments must be an array'
            ),

            'methods invalid format' => array(
                array(
                    'method' => array('name' => 'setValue')
                ),
                'InvalidArgumentException',
                'Method has to be passed as string'
            ),

            'constructor arguments invalid callback' => array(
                array(
                    'callback' => array('invalid', 'callback')
                ),
                'InvalidArgumentException',
                'Callback must be instance of \Magento\Validator\Constraint\Option\Callback'
            )
        );
    }

    /**
     * Check exception is thrown if validator is not an instance of \Magento\Validator\ValidatorInterface
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Constraint class "StdClass" must implement \Magento\Validator\ValidatorInterface
     */
    public function testCreateValidatorInvalidInstance()
    {
        $builder = $this->_objectManager->getObject(
            'Magento\Validator\Builder',
            array(
                'constraints' => array(array(
                    'alias' => 'alias',
                    'class' => 'StdClass',
                    'options' => null,
                    'type' => 'entity'
                )),
                'validatorFactory' => new \Magento\ValidatorFactory(new \Magento\ObjectManager\ObjectManager()),
            )
        );
        $builder->createValidator();
    }

    /**
     * Test invalid configuration formats
     *
     * @dataProvider invalidConfigurationFormatDataProvider
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Configuration has incorrect format
     *
     * @param mixed $configuration
     */
    public function testAddConfigurationInvalidFormat($configuration)
    {
        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Magento\Validator\Test\True',
            'options' => null,
            'type' => 'entity'
        ));
        /** @var $builder \Magento\Validator\Builder */
        $builder = $this->_objectManager->getObject(
            'Magento\Validator\Builder',
            array('constraints' => $constraints)
        );
        $builder->addConfigurations($configuration);
    }

    /**
     * Data provider for incorrect configurations
     *
     * @return array
     */
    public function invalidConfigurationFormatDataProvider()
    {
        return array(
            'configuration incorrect method call' => array(
                array(
                    'alias' => array(
                        'method' => array('name' => 'incorrectMethodCall')
                    )
                )
            ),

            'configuration incorrect configuration' => array(
                array(
                    'alias' => array(
                        array(
                            'data' => array('incorrectData')
                        )
                    )
                )
            )
        );
    }
}
