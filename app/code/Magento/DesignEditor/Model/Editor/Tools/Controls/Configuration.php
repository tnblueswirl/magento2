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
 * @package     Magento_DesignEditor
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration of controls
 */
namespace Magento\DesignEditor\Model\Editor\Tools\Controls;

class Configuration
{
    /**
     * Module name used for saving data to the view configuration
     */
    const SEPARATOR_MODULE = '::';

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventDispatcher;

    /**
     * @var \Magento\DesignEditor\Model\Config\Control\AbstractControl
     */
    protected $_configuration;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_design;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\Theme
     */
    protected $_theme;

    /**
     * @var \Magento\Core\Model\Theme
     */
    protected $_parentTheme;

    /**
     * @var \Magento\Config\View
     */
    protected $_viewConfig;

    /**
     * @var \Magento\Config\View
     */
    protected $_viewConfigParent;

    /**
     * Controls data
     *
     * @var array
     */
    protected $_data;

    /**
     * List of controls
     *
     * @var array
     */
    protected $_controlList = array();

    /**
     * View config model
     *
     * @var \Magento\View\ConfigInterface
     */
    protected $_viewConfigLoader;

    /**
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Event\ManagerInterface $eventDispatcher
     * @param \Magento\View\ConfigInterface $viewConfig
     * @param \Magento\DesignEditor\Model\Config\Control\AbstractControl $configuration
     * @param \Magento\View\Design\ThemeInterface $theme
     * @param \Magento\View\Design\ThemeInterface $parentTheme
     */
    public function __construct(
        \Magento\View\DesignInterface $design,
        \Magento\Filesystem $filesystem,
        \Magento\Event\ManagerInterface $eventDispatcher,
        \Magento\View\ConfigInterface $viewConfig,
        \Magento\DesignEditor\Model\Config\Control\AbstractControl $configuration = null,
        \Magento\View\Design\ThemeInterface $theme = null,
        \Magento\View\Design\ThemeInterface $parentTheme = null
    ) {
        $this->_configuration = $configuration;
        $this->_theme = $theme;
        $this->_parentTheme = $parentTheme ?: $theme->getParentTheme();
        $this->_design = $design;
        $this->_filesystem = $filesystem;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_viewConfigLoader = $viewConfig;
        $this->_initViewConfigs()->_loadControlsData();
    }

    /**
     * Initialize view configurations
     *
     * @return \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration
     */
    protected function _initViewConfigs()
    {
        $this->_viewConfig = $this->_viewConfigLoader->getViewConfig(array(
            'area'       => \Magento\View\DesignInterface::DEFAULT_AREA,
            'themeModel' => $this->_theme
        ));
        $this->_viewConfigParent = $this->_viewConfigLoader->getViewConfig(array(
            'area'       => \Magento\View\DesignInterface::DEFAULT_AREA,
            'themeModel' => $this->_parentTheme
        ));
        return $this;
    }

    /**
     * Load all control values
     *
     * @return \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration
     */
    protected function _loadControlsData()
    {
        $this->_data = $this->_configuration->getAllControlsData();
        $this->_prepareControlList($this->_data);
        foreach ($this->_controlList as &$control) {
            $this->_loadControlData($control, 'value', $this->_viewConfig);
            $this->_loadControlData($control, 'default', $this->_viewConfigParent);
        }
        return $this;
    }

    /**
     * Prepare list of control links
     *
     * @param array $controls
     * @return \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration
     */
    protected function _prepareControlList(array &$controls)
    {
        foreach ($controls as $controlName => &$control) {
            if (!empty($control['components'])) {
                $this->_prepareControlList($control['components']);
            }
            $this->_controlList[$controlName] = &$control;
        }
        return $this;
    }

    /**
     * Load data item values and default values from the view configuration
     *
     * @param array $control
     * @param string $paramName
     * @param \Magento\Config\View $viewConfiguration
     * @return \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration
     */
    protected function _loadControlData(array &$control, $paramName, \Magento\Config\View $viewConfiguration)
    {
        if (!empty($control['var'])) {
            list($module, $varKey) = $this->_extractModuleKey($control['var']);
            $control[$paramName] = $viewConfiguration->getVarValue($module, $varKey);
        }
        return $this;
    }

    /**
     * Load control data
     *
     * @return array
     */
    public function getAllControlsData()
    {
        return $this->_data;
    }

    /**
     * Get control data
     *
     * @param string $controlName
     * @return array
     * @throws \Magento\Core\Exception
     */
    public function getControlData($controlName)
    {
        if (!isset($this->_controlList[$controlName])) {
            throw new \Magento\Core\Exception("Unknown control: \"{$controlName}\"");
        }
        return $this->_controlList[$controlName];
    }

    /**
     * Extract module and key name
     *
     * @param string $value
     * @return array
     */
    protected function _extractModuleKey($value)
    {
        return explode(self::SEPARATOR_MODULE, $value);
    }

    /**
     * Extract var data keys for current controls configuration
     * array(module => array(varKey => array(controlName, controlValue)))
     *
     * @param array $controlsData
     * @param array $controls
     * @return array
     */
    protected function _prepareVarData(array $controlsData, array $controls)
    {
        $result = array();
        foreach ($controlsData as $controlName => $controlValue) {
            if (isset($controls[$controlName])) {
                list($module, $varKey) = $this->_extractModuleKey($controls[$controlName]['var']);
                $result[$module][$varKey] = array($controlName, $controlValue);
            }
        }
        return $result;
    }

    /**
     * Save control values data
     *
     * @param array $controlsData
     * @return \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration
     */
    public function saveData(array $controlsData)
    {
        $configDom = $this->_viewConfig->getDomConfigCopy()->getDom();
        $varData = $this->_prepareVarData($controlsData, $this->_controlList);

        /** @var $varsNode \DOMElement */
        foreach ($configDom->childNodes->item(0)->childNodes as $varsNode) {
            $moduleName = $varsNode->getAttribute('module');
            if (!isset($varData[$moduleName])) {
                continue;
            }
            /** @var $varNode \DOMElement */
            foreach ($varsNode->getElementsByTagName('var') as $varNode) {
                $varName = $varNode->getAttribute('name');
                if (isset($varData[$moduleName][$varName])) {
                    list($controlName, $controlValue) = $varData[$moduleName][$varName];
                    $varNode->nodeValue = $controlValue;
                    $this->_controlList[$controlName]['value'] = $controlValue;
                }
            }
        }
        $this->_saveViewConfiguration($configDom);
        $this->_eventDispatcher->dispatch('save_view_configuration', array(
            'configuration' => $this, 'theme' => $this->_theme
        ));
        return $this;
    }

    /**
     * Get control configuration
     *
     * @return \Magento\DesignEditor\Model\Config\Control\AbstractControl
     */
    public function getControlConfig()
    {
        return $this->_configuration;
    }

    /**
     * Save customized DOM of view configuration
     *
     * @param \DOMDocument $config
     * @return \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration
     */
    protected function _saveViewConfiguration(\DOMDocument $config)
    {
        $targetPath = $this->_theme->getCustomization()->getCustomViewConfigPath();
        $this->_filesystem->setIsAllowCreateDirectories(true)->write($targetPath, $config->saveXML());
        return $this;
    }
}
