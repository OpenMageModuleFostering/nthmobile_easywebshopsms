<?php

/**
 * Class NthMobile_EasyWebShopSMS_Block_Adminhtml_Easywebshopsmssaleabandonedcarts
 */
class NthMobile_EasyWebShopSMS_Block_Adminhtml_Easywebshopsmssaleabandonedcarts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_easywebshopsmssaleabandonedcarts';
        $this->_blockGroup = 'easywebshopsms';
        $this->_headerText = Mage::helper('easywebshopsms')->__('Sale Abandoned Carts to ALL');
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('notify', array(
            'label'     => Mage::helper('easywebshopsms')->__('Send notifications'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/notifyAll', array('store'  =>  $this->getRequest()->getParam('store', 0)))."')",
        ));
        $this->setTemplate('nthmobile/easywebshopsms/abandonedcarts/list.phtml');
    }

    /**
     * Prepare the layout
     */
    protected function _prepareLayout()
    {
        // Display store switcher if system has more one store
        if (!Mage::app()->isSingleStoreMode())
        {
            $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)))
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Getter for the store switcher HTML
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

}