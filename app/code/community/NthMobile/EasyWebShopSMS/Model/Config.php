<?php


class NthMobile_EasyWebShopSMS_Model_Config
{

    public function getStoreName()
    {
        return Mage::getStoreConfig('easywebshopsms/api_connection/storename');
    }

    public function getSmsOrigin()
    {
        return Mage::getStoreConfig('easywebshopsms/api_connection/sms_origin');
    }

    public function getNotifDelay()
    {
        $delay = Mage::getStoreConfig('easywebshopsms/abandoned_notifications/notify_delay');
        return date('Y-m-d H:i:s', time() - $delay * 24 * 3600);
    }

    /**
     * @return mixed
     */
    public function getCustomerGroupsLimitation()
    {
        return explode(',',Mage::getStoreConfig('easywebshopsms/abandoned_notifications/customer_groups'));
    }

     /**
     * checks if EasyWebShopSMS API module is enabled
     * @return boolean
     */
    public function isApiEnabled()
    {
        return (Mage::getStoreConfig('easywebshopsms/api_connection/active')==0) ? false : true;
    }

    public function isEventActive($eventConfig, $configGroup = "message_configuration")
    {
        $configGroup = $this->_resolveConfigGroup($configGroup);

        return (Mage::getStoreConfig('easywebshopsms/'.$configGroup.$eventConfig)==0) ? false : true;
    }

    public function getDryRun()
    {
        return (Mage::getStoreConfig('easywebshopsms/api_connection/dry_run')==1);
    }

     /**
     * getting SMS templates from config
     * @return string
     */
    public function getEventMessageTemplate($eventConfig, $configGroup = "message_configuration")
    {

        $configGroup = $this->_resolveConfigGroup($configGroup);
        if($this->isApiEnabled() && $this->isEventActive($eventConfig, $configGroup)) {
            return Mage::getStoreConfig('easywebshopsms/'.$configGroup.$eventConfig.'_text_message');
        }
        return "";
    }

    private function _resolveConfigGroup($group)
    {
        if(empty($group)) {
            return "";
        }
        $group = trim($group, "\\ /");
        return $group."/";
    }
}
