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
 * Image Sizing configuration
 */
namespace Magento\DesignEditor\Model\Config\Control;

class ImageSizing extends \Magento\DesignEditor\Model\Config\Control\AbstractControl
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array('title');

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_moduleReader;

    /**
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     * @param array $configFiles
     */
    public function __construct(\Magento\Core\Model\Config\Modules\Reader $moduleReader, array $configFiles)
    {
        $this->_moduleReader = $moduleReader;
        parent::__construct($configFiles);
    }

    /**
     * Path to quick_styles.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_moduleReader->getModuleDir('etc', 'Magento_DesignEditor') . DIRECTORY_SEPARATOR
            . 'image_sizing.xsd';
    }
}
