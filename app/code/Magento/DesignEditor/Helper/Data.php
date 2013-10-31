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
 * Design Editor main helper
 */
namespace Magento\DesignEditor\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * XML path to VDE front name setting
     *
     * @var string
     */
    protected $_frontName;

    /**
     * XML path to VDE disabled cache type setting
     *
     * @var array
     */
    protected $_disabledCacheTypes;

    /**
     * Parameter to indicate the translation mode (null, text, script, or alt).
     */
    const TRANSLATION_MODE = "translation_mode";

    /**
     * @var bool
     */
    protected $_isVdeRequest = false;

    /**
     * @var string
     */
    protected $_translationMode;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param $frontName
     * @param array $disabledCacheTypes
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        $frontName,
        array $disabledCacheTypes = array()
    ) {
        parent::__construct($context);
        $this->_frontName = $frontName;
        $this->_disabledCacheTypes = $disabledCacheTypes;
    }

    /**
     * Get VDE front name prefix
     *
     * @return string
     */
    public function getFrontName()
    {
        return $this->_frontName;
    }

    /**
     * Get disabled cache types in VDE mode
     *
     * @return array
     */
    public function getDisabledCacheTypes()
    {
        return $this->_disabledCacheTypes;
    }

    /**
     * This method returns an indicator of whether or not the current request is for vde
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    public function isVdeRequest(\Magento\App\RequestInterface $request = null)
    {
        if (null !== $request) {
            $result = false;
            $splitPath = explode('/', trim($request->getOriginalPathInfo(), '/'));
            if (count($splitPath) >= 3) {
                list($frontName, $currentMode, $themeId) = $splitPath;
                $result = $frontName === $this->getFrontName() && in_array($currentMode, $this->getAvailableModes())
                    && is_numeric($themeId);
            }
            $this->_isVdeRequest = $result;
        }
        return $this->_isVdeRequest;
    }

    /**
     * Get available modes for Design Editor
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return array(\Magento\DesignEditor\Model\State::MODE_NAVIGATION);
    }

    /**
     * Returns the translation mode the current request is in (null, text, script, or alt).
     *
     * @return mixed
     */
    public function getTranslationMode()
    {
        return $this->_translationMode;
    }

    /**
     * Sets the translation mode for the current request (null, text, script, or alt);
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\DesignEditor\Helper\Data
     */
    public function setTranslationMode(\Magento\App\RequestInterface $request)
    {
        $this->_translationMode = $request->getParam(self::TRANSLATION_MODE, null);
        return $this;
    }

    /**
     * Returns an indicator of whether or not inline translation is allowed in VDE.
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_translationMode !== null;
    }
}
