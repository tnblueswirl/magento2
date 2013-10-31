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
 * @package     Magento_ImportExport
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export customer address entity model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 * @todo refactor in the scope of https://wiki.magento.com/display/MAGE2/Technical+Debt+%28Team-Donetsk-B%29
 *
 * @method \Magento\Customer\Model\Resource\Address\Attribute\Collection getAttributeCollection() getAttributeCollection()
 */
namespace Magento\ImportExport\Model\Export\Entity\Eav\Customer;

class Address
    extends \Magento\ImportExport\Model\Export\Entity\AbstractEav
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute.
     * This name convention is for to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL      = '_email';
    const COLUMN_WEBSITE    = '_website';
    const COLUMN_ADDRESS_ID = '_entity_id';
    /**#@-*/

    /**#@+
     * Particular columns that contains of customer default addresses
     */
    const COLUMN_NAME_DEFAULT_BILLING  = '_address_default_billing_';
    const COLUMN_NAME_DEFAULT_SHIPPING = '_address_default_shipping_';
    /**#@-*/

    /**#@+
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME = 'Magento\Customer\Model\Resource\Address\Attribute\Collection';
    /**#@-*/

    /**#@+
     * XML path to page size parameter
     */
    const XML_PATH_PAGE_SIZE = 'export/customer_page_size/address';
    /**#@-*/

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COLUMN_WEBSITE, self::COLUMN_EMAIL, self::COLUMN_ADDRESS_ID);

    /**
     * Default addresses column names to appropriate customer attribute code
     *
     * @var array
     */
    protected static $_defaultAddressAttributeMapping = array(
        self::COLUMN_NAME_DEFAULT_BILLING  => 'default_billing',
        self::COLUMN_NAME_DEFAULT_SHIPPING => 'default_shipping'
    );

    /**
     * Customers whose addresses are exported
     *
     * @var \Magento\Customer\Model\Resource\Customer\Collection
     */
    protected $_customerCollection;

    /**
     * Customer addresses collection
     *
     * @var \Magento\Customer\Model\Resource\Address\Collection
     */
    protected $_addressCollection;

    /**
     * Customers whose address are exported
     *
     * @var \Magento\ImportExport\Model\Export\Entity\Eav\Customer
     */
    protected $_customerEntity;

    /**
     * Existing customers information. In form of:
     *
     * [customer e-mail] => array(
     *    [website id 1] => customer_id 1,
     *    [website id 2] => customer_id 2,
     *           ...       =>     ...      ,
     *    [website id n] => customer_id n,
     * )
     *
     * @var array
     */
    protected $_customers = array();

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\App $app
     * @param \Magento\ImportExport\Model\Export\Factory $collectionFactory
     * @param \Magento\ImportExport\Model\Resource\CollectionByPagesIteratorFactory $resourceColFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Resource\Customer\CollectionFactory $customerColFactory
     * @param \Magento\ImportExport\Model\Export\Entity\Eav\CustomerFactory $eavCustomerFactory
     * @param \Magento\Customer\Model\Resource\Address\CollectionFactory $addressColFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\App $app,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\ImportExport\Model\Resource\CollectionByPagesIteratorFactory $resourceColFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Resource\Customer\CollectionFactory $customerColFactory,
        \Magento\ImportExport\Model\Export\Entity\Eav\CustomerFactory $eavCustomerFactory,
        \Magento\Customer\Model\Resource\Address\CollectionFactory $addressColFactory,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $app, $collectionFactory, $resourceColFactory, $locale, $eavConfig,
            $data);

        $this->_customerCollection = isset($data['customer_collection']) ? $data['customer_collection']
            : $customerColFactory->create();

        $this->_customerEntity = isset($data['customer_entity']) ? $data['customer_entity']
            : $eavCustomerFactory->create();

        $this->_addressCollection = isset($data['address_collection']) ? $data['address_collection']
            : $addressColFactory->create();

        $this->_initWebsites(true);
        $this->setFileName($this->getEntityTypeCode());
    }

    /**
     * Initialize existent customers data
     *
     * @return \Magento\ImportExport\Model\Export\Entity\Eav\Customer\Address
     */
    protected function _initCustomers()
    {
        if (empty($this->_customers)) {
            // add customer default addresses column name to customer attribute mapping array
            $this->_customerCollection->addAttributeToSelect(self::$_defaultAddressAttributeMapping);
            // filter customer collection
            $this->_customerCollection = $this->_customerEntity->filterEntityCollection($this->_customerCollection);

            $customers = array();
            $addCustomer = function (\Magento\Customer\Model\Customer $customer) use (&$customers) {
                $customers[$customer->getId()] = $customer->getData();
            };

            $this->_byPagesIterator->iterate($this->_customerCollection, $this->_pageSize, array($addCustomer));
            $this->_customers = $customers;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _getHeaderColumns()
    {
        return array_merge(
            $this->_permanentAttributes,
            $this->_getExportAttributeCodes(),
            array_keys(self::$_defaultAddressAttributeMapping)
        );
    }

    /**
     * Get customers collection
     *
     * @return \Magento\Customer\Model\Resource\Address\Collection
     */
    protected function _getEntityCollection()
    {
        return $this->_addressCollection;
    }

    /**
     * Export process
     *
     * @return string
     */
    public function export()
    {
        // skip and filter by customer address attributes
        $this->_prepareEntityCollection($this->_getEntityCollection());
        $this->_getEntityCollection()->setCustomerFilter(array_keys($this->_customers));

        // prepare headers
        $this->getWriter()->setHeaderCols($this->_getHeaderColumns());

        $this->_exportCollectionByPages($this->_getEntityCollection());

        return $this->getWriter()->getContents();
    }

    /**
     * Export given customer address data plus related customer data (required for import)
     *
     * @param \Magento\Customer\Model\Address $item
     * @return string
     */
    public function exportItem($item)
    {
        $row = $this->_addAttributeValuesToRow($item);

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->_customers[$item->getParentId()];

        // Fill row with default address attributes values
        foreach (self::$_defaultAddressAttributeMapping as $columnName => $attributeCode) {
            if (!empty($customer[$attributeCode]) && ($customer[$attributeCode] == $item->getId())) {
                $row[$columnName] = 1;
            }
        }

        // Unique key
        $row[self::COLUMN_ADDRESS_ID] = $item['entity_id'];
        $row[self::COLUMN_EMAIL]      = $customer['email'];
        $row[self::COLUMN_WEBSITE]    = $this->_websiteIdToCode[$customer['website_id']];

        $this->getWriter()
            ->writeRow($row);
    }

    /**
     * Set parameters (push filters from post into export customer model)
     *
     * @param array $parameters
     * @return \Magento\ImportExport\Model\Export\Entity\Eav\Customer\Address
     */
    public function setParameters(array $parameters)
    {
        //  push filters from post into export customer model
        $this->_customerEntity->setParameters($parameters);
        $this->_initCustomers();

        return parent::setParameters($parameters);
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return $this->getAttributeCollection()->getEntityTypeCode();
    }
}
