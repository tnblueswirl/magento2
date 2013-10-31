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
 * @package     Magento_Index
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Index Process Collection
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Index\Model\Resource\Process;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Index\Model\Process', 'Magento\Index\Model\Resource\Process');
    }

    /**
     * Add count of unprocessed events to process collection
     *
     * @return \Magento\Index\Model\Resource\Process\Collection
     */
    public function addEventsStats()
    {
        $countsSelect = $this->getConnection()
            ->select()
            ->from($this->getTable('index_process_event'), array('process_id', 'events' => 'COUNT(*)'))
            ->where('status=?', \Magento\Index\Model\Process::EVENT_STATUS_NEW)
            ->group('process_id');
        $this->getSelect()
            ->joinLeft(
                array('e' => $countsSelect),
                'e.process_id=main_table.process_id',
                array('events' => $this->getConnection()->getCheckSql(
                    $this->getConnection()->prepareSqlCondition('e.events', array('null' => null)), 0, 'e.events'
                ))
            );
        return $this;
    }
}
