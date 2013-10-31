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
 * @package     Magento_Core
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Theme collection
 */
namespace Magento\Core\Model\Resource\Theme;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Default page size
     */
    const DEFAULT_PAGE_SIZE = 6;

    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Theme', 'Magento\Core\Model\Resource\Theme');
    }

    /**
     * Add title for parent themes
     *
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    public function addParentTitle()
    {
        $this->getSelect()->joinLeft(
            array('parent' => $this->getMainTable()),
            'main_table.parent_id = parent.theme_id',
            array('parent_theme_title' => 'parent.theme_title')
        );
        return $this;
    }

    /**
     * Add area filter
     *
     * @param string $area
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    public function addAreaFilter($area = \Magento\Core\Model\App\Area::AREA_FRONTEND)
    {
        $this->getSelect()->where('main_table.area=?', $area);
        return $this;
    }

    /**
     * Add type filter in relations
     *
     * @param int $typeParent
     * @param int $typeChild
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    public function addTypeRelationFilter($typeParent, $typeChild)
    {
        $this->getSelect()->join(
            array('parent' => $this->getMainTable()),
            'main_table.parent_id = parent.theme_id',
            array('parent_type' => 'parent.type')
        )->where('parent.type = ?', $typeParent)->where('main_table.type = ?', $typeChild);
        return $this;
    }

    /**
     * Add type filter
     *
     * @param string|array $type
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    public function addTypeFilter($type)
    {
        $this->addFieldToFilter('main_table.type', array('in' => $type));
        return $this;
    }

    /**
     * Filter visible themes in backend (physical and virtual only)
     *
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    public function filterVisibleThemes()
    {
        $this->addTypeFilter(array(\Magento\Core\Model\Theme::TYPE_PHYSICAL, \Magento\Core\Model\Theme::TYPE_VIRTUAL));
        return $this;
    }

    /**
     * Return array for select field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('theme_id', 'theme_title');
    }

    /**
     * Return array for grid column
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('theme_id', 'theme_title');
    }

    /**
     * Get theme from DB by area and theme_path
     *
     * @param string $fullPath
     * @return \Magento\Core\Model\Theme
     */
    public function getThemeByFullPath($fullPath)
    {
        $this->_reset()->clear();
        list($area, $themePath) = explode('/', $fullPath, 2);
        $this->addFieldToFilter('area', $area);
        $this->addFieldToFilter('theme_path', $themePath);

        return $this->getFirstItem();
    }

    /**
     * Set page size
     *
     * @param int $size
     * @return $this
     */
    public function setPageSize($size = self::DEFAULT_PAGE_SIZE)
    {
        return parent::setPageSize($size);
    }

    /**
     * Update all child themes relations
     *
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @return $this
     */
    public function updateChildRelations(\Magento\View\Design\ThemeInterface $themeModel)
    {
        $parentThemeId = $themeModel->getParentId();
        $this->addFieldToFilter('parent_id', array('eq' => $themeModel->getId()))->load();

        /** @var $theme \Magento\View\Design\ThemeInterface */
        foreach ($this->getItems() as $theme) {
            $theme->setParentId($parentThemeId)->save();
        }
        return $this;
    }

    /**
     * Filter frontend physical theme.
     * All themes or per page if set page and page size (page size is optional)
     *
     * @param int $page
     * @param int $pageSize
     * @return $this
     */
    public function filterPhysicalThemes(
        $page = null,
        $pageSize = \Magento\Core\Model\Resource\Theme\Collection::DEFAULT_PAGE_SIZE
    ) {

        $this->addAreaFilter(\Magento\Core\Model\App\Area::AREA_FRONTEND)
            ->addTypeFilter(\Magento\Core\Model\Theme::TYPE_PHYSICAL);
        if ($page) {
            $this->setPageSize($pageSize)->setCurPage($page);
        }
        return $this;
    }

    /**
     * Filter theme customization
     *
     * @param string $area
     * @param int $type
     * @return $this
     */
    public function filterThemeCustomizations(
        $area = \Magento\Core\Model\App\Area::AREA_FRONTEND,
        $type = \Magento\Core\Model\Theme::TYPE_VIRTUAL
    ) {
        $this->addAreaFilter($area)->addTypeFilter($type);
        return $this;
    }
}
