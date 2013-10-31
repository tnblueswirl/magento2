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
 * @package     Magento_Directory
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Directory\Model\Config\Source;

class Allregion implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_countries;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\Directory\Model\Resource\Country\CollectionFactory
     */
    protected $_countryCollFactory;

    /**
     * @var \Magento\Directory\Model\Resource\Region\CollectionFactory
     */
    protected $_regionCollFactory;

    /**
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory
     */
    public function __construct(
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory
    ) {
        $this->_countryCollFactory = $countryCollFactory;
        $this->_regionCollFactory = $regionCollFactory;
    }

    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $countriesArray = $this->_countryCollFactory->create()->load()
                ->toOptionArray(false);
            $this->_countries = array();
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = array();
            $regionsCollection = $this->_regionCollFactory->create()->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getId()] = $region->getDefaultName();
            }
            uksort($countryRegions, array($this, 'sortRegionCountries'));

            $this->_options = array();
            foreach ($countryRegions as $countryId => $regions) {
                $regionOptions = array();
                foreach ($regions as $regionId => $regionName) {
                    $regionOptions[] = array('label' => $regionName, 'value' => $regionId);
                }
                $this->_options[] = array('label' => $this->_countries[$countryId], 'value' => $regionOptions);
            }
        }
        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value' => '', 'label' => ''));
        }

        return $options;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    public function sortRegionCountries($a, $b)
    {
        return strcmp($this->_countries[$a], $this->_countries[$b]);
    }
}
