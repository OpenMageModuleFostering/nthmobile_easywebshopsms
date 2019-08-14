<?php


class  NthMobile_EasyWebShopSMS_Block_Adminhtml_System_Config_Section_Details
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @var string
     */
    protected $_template = 'nthmobile/easywebshopsms/system/config/section/details.phtml';

    /**
     * @return string
     */
    public function getSupportLink()
    {
        return 'http://easyshopsms.nthmobile.com';
    }

    public function getSignUpUrl()
    {
        return 'http://easyshopsms.nthmobile.com';
    }

    /**
     * Checks if the account details have not been filled in
     *
     * @return bool
     */
    public function accountDetailsNotFilledIn()
    {
        return (empty(Mage::getStoreConfig('easywebshopsms/api_connection/api_host'))
                || empty(Mage::getStoreConfig('easywebshopsms/api_connection/api_user'))
                || empty(Mage::getStoreConfig('easywebshopsms/api_connection/api_pass'))
                || empty(Mage::getStoreConfig('easywebshopsms/api_connection/api_port'))
        );
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

}