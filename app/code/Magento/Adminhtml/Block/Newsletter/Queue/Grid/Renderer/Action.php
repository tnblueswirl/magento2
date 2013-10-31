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
 * @package     Magento_Adminhtml
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Newsletter\Queue\Grid\Renderer;

class Action extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Action
{
    public function render(\Magento\Object $row)
    {
        $actions = array();

        if($row->getQueueStatus()==\Magento\Newsletter\Model\Queue::STATUS_NEVER) {
               if(!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
                $actions[] = array(
                    'url' => $this->getUrl('*/*/start', array('id'=>$row->getId())),
                    'caption'	=> __('Start')
                );
            }
        } else if ($row->getQueueStatus()==\Magento\Newsletter\Model\Queue::STATUS_SENDING) {
            $actions[] = array(
                    'url' => $this->getUrl('*/*/pause', array('id'=>$row->getId())),
                    'caption'	=>	__('Pause')
            );

            $actions[] = array(
                'url'		=>	$this->getUrl('*/*/cancel', array('id'=>$row->getId())),
                'confirm'	=>	__('Do you really want to cancel the queue?'),
                'caption'	=>	__('Cancel')
            );


        } else if ($row->getQueueStatus()==\Magento\Newsletter\Model\Queue::STATUS_PAUSE) {

            $actions[] = array(
                'url' => $this->getUrl('*/*/resume', array('id'=>$row->getId())),
                'caption'	=>	__('Resume')
            );

        }

        $actions[] = array(
            'url'       =>  $this->getUrl('*/newsletter_queue/preview',array('id'=>$row->getId())),
            'caption'   =>  __('Preview'),
            'popup'     =>  true
        );

        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
