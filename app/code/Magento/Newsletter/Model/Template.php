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
 * @package     Magento_Newsletter
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template model
 *
 * @method \Magento\Newsletter\Model\Resource\Template _getResource()
 * @method \Magento\Newsletter\Model\Resource\Template getResource()
 * @method string getTemplateCode()
 * @method \Magento\Newsletter\Model\Template setTemplateCode(string $value)
 * @method \Magento\Newsletter\Model\Template setTemplateText(string $value)
 * @method \Magento\Newsletter\Model\Template setTemplateTextPreprocessed(string $value)
 * @method string getTemplateStyles()
 * @method \Magento\Newsletter\Model\Template setTemplateStyles(string $value)
 * @method int getTemplateType()
 * @method \Magento\Newsletter\Model\Template setTemplateType(int $value)
 * @method string getTemplateSubject()
 * @method \Magento\Newsletter\Model\Template setTemplateSubject(string $value)
 * @method string getTemplateSenderName()
 * @method \Magento\Newsletter\Model\Template setTemplateSenderName(string $value)
 * @method string getTemplateSenderEmail()
 * @method \Magento\Newsletter\Model\Template setTemplateSenderEmail(string $value)
 * @method int getTemplateActual()
 * @method \Magento\Newsletter\Model\Template setTemplateActual(int $value)
 * @method string getAddedAt()
 * @method \Magento\Newsletter\Model\Template setAddedAt(string $value)
 * @method string getModifiedAt()
 * @method \Magento\Newsletter\Model\Template setModifiedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Model;

class Template extends \Magento\Core\Model\Template
{
    /**
     * Template Text Preprocessed flag
     *
     * @var bool
     */
    protected $_preprocessFlag = false;

    /**
     * Mail object
     *
     * @var \Zend_Mail
     */
    protected $_mail;

    /**
     * Store manager to emulate design
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Http-request, used to determine current store in multi-store mode
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Filter for newsletter text
     *
     * @var \Magento\Newsletter\Model\Template\Filter
     */
    protected $_templateFilter;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Template factory
     *
     * @var \Magento\Newsletter\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Newsletter\Model\Template\Filter $filter
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\Newsletter\Model\TemplateFactory $templateFactory
     * @param \Magento\Core\Model\App\Emulation $appEmulation
     * @param array $data
     */
    public function __construct(
        \Magento\View\DesignInterface $design,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\RequestInterface $request,
        \Magento\Newsletter\Model\Template\Filter $filter,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Newsletter\Model\TemplateFactory $templateFactory,
        \Magento\Core\Model\App\Emulation $appEmulation,
        array $data = array()
    ) {
        parent::__construct($design, $context, $registry, $appEmulation, $storeManager, $data);
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_filter = $filter;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Newsletter\Model\Resource\Template');
    }

    /**
     * Validate Newsletter template
     *
     * @return bool
     * @throws \Magento\Core\Exception
     */
    public function validate()
    {
        $validators = array(
            'template_code'         => array(\Zend_Filter_Input::ALLOW_EMPTY => false),
            'template_type'         => 'Int',
            'template_sender_email' => 'EmailAddress',
            'template_sender_name'  => array(\Zend_Filter_Input::ALLOW_EMPTY => false)
        );
        $data = array();
        foreach (array_keys($validators) as $validateField) {
            $data[$validateField] = $this->getDataUsingMethod($validateField);
        }

        $validateInput = new \Zend_Filter_Input(array(), $validators, $data);
        if (!$validateInput->isValid()) {
            $errorMessages = array();
            foreach ($validateInput->getMessages() as $messages) {
                if (is_array($messages)) {
                    foreach ($messages as $message) {
                        $errorMessages[] = $message;
                    }
                } else {
                    $errorMessages[] = $messages;
                }
            }

            throw new \Magento\Core\Exception(join("\n", $errorMessages));
        }
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Newsletter\Model\Template
     */
    protected function _beforeSave()
    {
        $this->validate();
        return parent::_beforeSave();
    }

    /**
     * Load template by code
     *
     * @param string $templateCode
     * @return \Magento\Newsletter\Model\Template
     */
    public function loadByCode($templateCode)
    {
        $this->_getResource()->loadByCode($this, $templateCode);
        return $this;
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType()
    {
        return $this->getTemplateType();
    }

    /**
     * Check is Preprocessed
     *
     * @return bool
     */
    public function isPreprocessed()
    {
        return strlen($this->getTemplateTextPreprocessed()) > 0;
    }

    /**
     * Check Template Text Preprocessed
     *
     * @return bool
     */
    public function getTemplateTextPreprocessed()
    {
        if ($this->_preprocessFlag) {
            $this->setTemplateTextPreprocessed($this->getProcessedTemplate());
        }

        return $this->getData('template_text_preprocessed');
    }

    /**
     * Retrieve processed template
     *
     * @param array $variables
     * @param bool $usePreprocess
     * @return string
     */
    public function getProcessedTemplate(array $variables = array(), $usePreprocess = false)
    {
        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        if ($this->_storeManager->hasSingleStore()) {
            $this->_filter->setStoreId($this->_storeManager->getStore()->getId());
        } else {
            $this->_filter->setStoreId($this->_request->getParam('store_id'));
        }

        $this->_filter
            ->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables);

        if ($usePreprocess && $this->isPreprocessed()) {
            return $this->_filter->filter($this->getPreparedTemplateText(true));
        }

        return $this->_filter->filter($this->getPreparedTemplateText());
    }

    /**
     * Makes additional text preparations for HTML templates
     *
     * @param bool $usePreprocess Use Preprocessed text or original text
     * @return string
     */
    public function getPreparedTemplateText($usePreprocess = false)
    {
        $text = $usePreprocess ? $this->getTemplateTextPreprocessed() : $this->getTemplateText();

        if ($this->_preprocessFlag || $this->isPlain() || !$this->getTemplateStyles()) {
            return $text;
        }
        // wrap styles into style tag
        $html = "<style type=\"text/css\">\n%s\n</style>\n%s";
        return sprintf($html, $this->getTemplateStyles(), $text);
    }

    /**
     * Retrieve included template
     *
     * @param string $templateCode
     * @param array $variables
     * @return string
     */
    public function getInclude($templateCode, array $variables)
    {
        /** @var \Magento\Newsletter\Model\Template $template */
        $template = $this->_templateFactory->create();
        $template->loadByCode($templateCode)
            ->getProcessedTemplate($variables);
        return $template;
    }

    /**
     * Retrieve processed template subject
     *
     * @param array $variables
     * @return string
     */
    public function getProcessedTemplateSubject(array $variables)
    {
        $processor = new \Magento\Filter\Template();

        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        $processor->setVariables($variables);
        return $processor->filter($this->getTemplateSubject());
    }

    /**
     * Retrieve template text wrapper
     *
     * @return string
     */
    public function getTemplateText()
    {
        if (!$this->getData('template_text') && !$this->getId()) {
            $this->setData('template_text',
                __('Follow this link to unsubscribe <!-- This tag is for unsubscribe link  -->'
                    . '<a href="{{var subscriber.getUnsubscriptionLink()}}">{{var subscriber.getUnsubscriptionLink()}}'
                    . '</a>'));
        }

        return $this->getData('template_text');
    }

    /**
     * Check if template can be added to newsletter queue
     *
     * @return boolean
     */
    public function isValidForSend()
    {
        return !$this->_coreStoreConfig->getConfigFlag(\Magento\Core\Helper\Data::XML_PATH_SYSTEM_SMTP_DISABLE)
            && $this->getTemplateSenderName()
            && $this->getTemplateSenderEmail()
            && $this->getTemplateSubject();
    }
}
