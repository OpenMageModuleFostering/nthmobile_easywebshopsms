<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 31.5.2016.
 * Time: 9:48
 */
class NthMobile_EasyWebShopSMS_Adminhtml_EasywebshopsmsabandonedcartsController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @return NthMobile_EasyWebShopSMS_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel('easywebshopsms/config');
    }


    public function indexAction()
    {
        $this->_title($this->__('Easywebshopsms'))->_title($this->__('Abandoned carts'));
        $this->loadLayout();
        $this->_setActiveMenu('easywebshopsms/easywebshopsms');
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('easywebshopsms/adminhtml_easywebshopsmsabandonedcarts_grid')->toHtml()
        );
    }

    public function notifyAllAction()
    {
        try {
            $count = Mage::getModel('easywebshopsms/notifier')->sendAbandonedCartsSMS();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('easywebshopsms')->__(
                    '%sTotal of %d customer(s) were successfully notified', ($this->getConfig()->getDryRun() ? "<b>!DRY RUN!</b> " : ""), $count
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }


    public function notifyAction()
    {
        $customer_mobiles = $this->getRequest()->getParam($this->getRequest()->getParam('massaction_prepare_key', 'easywebshopsmsabandonedcarts'));
        if (!is_array($customer_mobiles)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('easywebshopsms')->__('Please select customers(s)'));
        } else {

            try {
                $count =  Mage::getModel('easywebshopsms/notifier')->sendAbandonedCartsSMS($customer_mobiles);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('easywebshopsms')->__(
                        '%sTotal of %d customer(s) were successfully notified', ($this->getConfig()->getDryRun() ? "<b>!DRY RUN!</b> " : ""), $count
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}