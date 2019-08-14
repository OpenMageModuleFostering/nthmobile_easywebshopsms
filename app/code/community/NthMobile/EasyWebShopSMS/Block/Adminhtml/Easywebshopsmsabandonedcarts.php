<?php

/**
 * Class NthMobile_EasyWebShopSMS_Block_Adminhtml_Abandonedcarts
 */
class NthMobile_EasyWebShopSMS_Block_Adminhtml_Easywebshopsmsabandonedcarts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_easywebshopsmsabandonedcarts';
        $this->_blockGroup = 'easywebshopsms';
        $this->_headerText = Mage::helper('easywebshopsms')->__('Abandoned Carts (Applied delay: %s days)', Mage::getStoreConfig('abandonedcartsconfig/options/notify_delay'));
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('notify', array(
            'label'     => Mage::helper('easywebshopsms')->__('Send SMS notifications to ALL'),
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