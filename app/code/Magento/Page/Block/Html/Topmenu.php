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
 * @package     Magento_Page
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Top menu block
 *
 * @category    Magento
 * @package     Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Page\Block\Html;

class Topmenu extends \Magento\Core\Block\Template
{
    /**
     * Top menu data tree
     *
     * @var \Magento\Data\Tree\Node
     */
    protected $_menu;

    /**
     * Init top menu tree structure
     */
    public function _construct()
    {
        $this->_menu = new \Magento\Data\Tree\Node(array(), 'root', new \Magento\Data\Tree());

        // enabling the cache for this topmenu to not expire until changes made in admin area
        // this is to prevent the menu from being rebuild every request and to prevent new categories from showing up
        // immediately
        $this->addData(array(
            'cache_lifetime'    => false,
            'cache_tags'        => array(
                \Magento\Core\Model\Store\Group::CACHE_TAG
            ),
        ));
    }

    /**
     * Get top menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        $this->_eventManager->dispatch('page_block_html_topmenu_gethtml_before', array(
            'menu' => $this->_menu
        ));

        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);

        $transportObject = new \Magento\Object(array('html' => $html));
        $this->_eventManager->dispatch('page_block_html_topmenu_gethtml_after', array(
            'menu'            => $this->_menu,
            'transportObject' => $transportObject,
        ));

        return $html;
    }

    /**
     * Count All Subnavigation Items
     *
     * @param \Magento\Backend\Model\Menu $items
     * @return int
     */
    protected function _countItems($items)
    {
        $total = $items->count();
        foreach ($items as $item) {
            /** @var $item \Magento\Backend\Model\Menu\Item */
            if ($item->hasChildren()) {
                $total += $this->_countItems($item->getChildren());
            }
        }
        return $total;
    }

    /**
     * Building Array with Column Brake Stops
     *
     * @param \Magento\Backend\Model\Menu $items
     * @param int $limit
     * @return array
     * @todo: Add Depth Level limit, and better logic for columns
     */
    protected function _columnBrake($items, $limit)
    {
        $total = $this->_countItems($items);

        if ($total <= $limit) {
            return;
        }
        $result[] = array(
                'total' => $total,
                'max'   => (int)ceil($total / ceil($total / $limit))
            );

        $count = 0;
        $firstCol = true;
        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;
            if ($place >= $limit) {
                $colbrake = !$firstCol;
                $count = 0;
            } elseif ($count >= $limit) {
                $colbrake = !$firstCol;
                $count = $place;
            } else {
                $colbrake = false;
            }
            $result[] = array(
                'place' => $place,
                'colbrake' => $colbrake
            );
            $firstCol = false;
        }
        return $result;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param $menuItem \Magento\Backend\Model\Menu\Item
     * @param $level int
     * @param $limit int
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }
        if (!empty($childrenWrapClass)) {
            $html .= '<div class="' . $childrenWrapClass . '">';
        }
        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }
        $html .= '<ul class="level' . $childLevel . '">';
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        if (!empty($childrenWrapClass)) {
            $html .= '</div>';
        }
        return $html;
    }


    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(\Magento\Data\Tree\Node $menuTree, $childrenWrapClass, $limit, $colBrakes = array())
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName()) . '</span></a>'
                . $this->_addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
                . '</li>';
            $itemPosition++;
            $counter++;
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param \Magento\Data\Tree\Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(\Magento\Data\Tree\Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);

        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }

        return $html;
    }

    /**
     * Returns array of menu item's attributes
     *
     * @param \Magento\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemAttributes(\Magento\Data\Tree\Node $item)
    {
        $menuItemClasses = $this->_getMenuItemClasses($item);
        $attributes = array(
            'class' => implode(' ', $menuItemClasses)
        );

        return $attributes;
    }

    /**
     * Returns array of menu item's classes
     *
     * @param \Magento\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemClasses(\Magento\Data\Tree\Node $item)
    {
        $classes = array();

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        return $classes;
    }
}
