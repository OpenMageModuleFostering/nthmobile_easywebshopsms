<?php

class NthMobile_EasyWebShopSMS_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $logFileName = 'easywebshopsms.log';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($text, $contextData = array())
    {
        if(!is_null($contextData) && is_array($contextData)) {
            $text .=  "__CTX__".json_encode($contextData);
        }
        $text .= "__EOL__";
        Mage::log($text, null, $this->logFileName, true);
    }

    public function getLogFile()
    {
        $logDir = Mage::getBaseDir('var') . DS . 'log';
        $logFile = $logDir . DS . $this->logFileName;
        return $logFile;
    }

    public function registerEvent($data, $text)
    {

        $origin = Mage::getModel('easywebshopsms/config')->getSmsOrigin();

        $client = Mage::getModel('easywebshopsms/bulkclient')->getClient();
        $customer     = Mage::getSingleton('customer/session');
        $customer_id = array_get($data,'customer_id', array_get($data,'data.customer_id', null));

        if ($customer_id == null && $customer->isLoggedIn()) {
            $customer_id = $customer->getId();
        }

        if($customer_id != null) {
            $customerData = Mage::getModel('customer/customer')->load($customer_id); //->getData(); //@XXX use getData to get Array of data

            $msisdn = $customerData->getMobileNumber();

            $text = $this->prepareMessage($text, $data);
            $logData   = [
                'customer_id'    => $customerData->getId(),
                'event_name'     => $data['event'],
                'sms_origin'     => $origin,
                'mobile_number'  => $msisdn,
                'message_text'   => $text,
                'time'           => time()
            ];
            $this->log("[SMS] From: $origin, To: $msisdn :: $text" , $logData);

            if(Mage::getModel('easywebshopsms/config')->getDryRun()) {
                return true;
            }
            if(empty($msisdn)) {
                return false;
            }
            $response = $client->textMessage()->send($origin, $msisdn, $text, [
                // Handle notification with this URL. Notification is POST request
                'status_url' => Mage::app()->getStore()->getUrl('/easywebshopsms/api/handleBulkSmsNotification')
            ]);

            if ($response->isOk()) {

                $textMessage = $response->resource();

                $inputData   = [
                    'customer_id'    => $customerData->getId(),
                    'event_name'     => $data['event'],
                    'log_time'       => Mage::getModel('core/date')->date('Y-m-d H:i:s'),
                    'sms_origin'     => $origin,
                    'mobile_number'  => $msisdn,
                    'message_text'   => $text,
                    'sms_status_code'=> $textMessage->smsId()
                ];

                /*
                 *sms_sent = integer (0 || 1)
                 * sms_status_code = integer(Gatewway status code)
                 * sms_status_message = string (200)
                 * */

                Mage::getModel('easywebshopsms/easywebshopsms')
                    ->setData($inputData)
                    ->save();

                return true;
            } else {
                $error   = $response->error();

                /*$message = $error->message;
                $code    = $error->code;*/
            }
        }
        return false;
    }

    public function getOrderData($order)
    {
        //getting last tracking number
        $tracking = Mage::getResourceModel('sales/order_shipment_track_collection')->setOrderFilter($order)->getData();

        if (!empty($tracking)) {
            $last = count($tracking)-1;
            $last_tracking_number = $tracking[$last]['track_number'];
        } else
            $last_tracking_number = 'no_tracking';

        //getting order data to generate message template
        $messageOrderData['order']['number'] = $order->getIncrement_id();
        $messageOrderData['order']['status']  = $order->getOrigData('status');
        $messageOrderData['order']['status_new']  = $order->getData('status');
        $messageOrderData['tracking']['number'] = $last_tracking_number;
        $messageOrderData['storename'] = Mage::getModel('easywebshopsms/config')->getStoreName();
        $messageOrderData['customer_id'] = $order->getCustomerId();

        return $messageOrderData;
    }

    public function prepareMessage($text, $data)
    {
        $newText = $this->sprintargs($text,$this->array_flatten($data));
        return $newText;
    }

    public function prepareEventData($eventName, $data)
    {
        $customer     = Mage::getSingleton('customer/session');
        $customerData = [];
        if ($customer->isLoggedIn()) {
            $customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
        }

        $prepared = [
            'event'    => $eventName,
            'customer' => $customerData,
            'data'     => $data
        ];

        return $prepared;
    }

    public function sprintargs($str = '', $vars = [], $charStart = '{', $charEnd = '}')
    {
        if (!$str) {
            return '';
        }
        if (count($vars) > 0) {
            foreach ($vars as $k => $v) {
                $str = str_replace($charStart . $k . $charEnd, $v, $str);
            }
        }
        return $str;
    }

    public function array_flatten($array, $prefix = '')
    {
        $result = [];
        if (is_null($array)) {
            return $result;
        }
        if (is_object($array)) {
            $array = json_decode(json_encode($array), true);
        }
        if (!is_array($array)) {
            return $result;
        }
        foreach ($array as $key => $value) {
            if (!is_scalar($value) && !is_null($value)) {
                $result = array_merge($result, $this->array_flatten($value, "$prefix$key."));
            } else {
                $result["$prefix$key"] = (is_null($value)) ? "" : $value;
            }
        }

        return $result;
    }

}

