<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 31.5.2016.
 * Time: 10:03
 */
class NthMobile_EasyWebShopSMS_Block_Adminhtml_Easywebshopsmslogs extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_easywebshopsmslogs';
        $this->_blockGroup = 'easywebshopsms';
        $this->_headerText = Mage::helper('easywebshopsms')->__('Message Log');
        parent::__construct();
        $this->_removeButton('add');
    }
}