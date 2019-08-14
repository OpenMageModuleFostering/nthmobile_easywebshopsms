<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 31.5.2016.
 * Time: 9:48
 */
class NthMobile_EasyWebShopSMS_Adminhtml_EasywebshopsmslogsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Easywebshopsms'))->_title($this->__('Message Log'));
        $this->loadLayout();
        $this->_setActiveMenu('easywebshopsms/easywebshopsms');
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('easywebshopsms/adminhtml_easywebshopsmslogs_grid')->toHtml()
        );
    }

    public function resetLogFileAction() {
        $logFile = Mage::helper('easywebshopsms')->getLogFile();
        $ret = "";
        if(file_exists($logFile)) {
            file_put_contents($logFile, "");
            $ret = nl2br(file_put_contents($logFile));
        }
        Mage::app()->getResponse()->setBody($ret);
    }
}