<?php

/**
 * @author Nikola Bakos <nikola.bakos@nth.ch>
 *
 * Paths follow the following pattern:
 * /:module/:controller/:action
 *
 * :module is what matches package/module/config.xml
 *         config.frontend.routers.{module}.args.frontName value,
 *         and in this case 'easywebshopsms'
 *
 * :controller defaults to index
 * :action defaults to index
 *
 */
class NthMobile_EasyWebShopSMS_ApiController extends Mage_Core_Controller_Front_Action
{

    /**
     * paths: /easywebshopsms/
     *        /easywebshopsms/api/handleBulkSmsNotification
     *        /easywebshopsms/api/relatedProducts
     *
     * @return void
     */
    public function relatedProductsAction()
    {
        $productId       = Mage::app()->getRequest()->getParam('productId');
        $relatedProducts = [];

        if ($productId != null) {
            $product           = Mage::getModel('catalog/product')->load($productId);
            $relatedProductsId = $product->getRelatedProductIds();
            foreach ($relatedProductsId as $relatedProductId) {
                $relatedProducts[] = Mage::getModel('catalog/product')->load($relatedProductId)->getData();
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($relatedProducts));
        $this->getResponse()->setHeader('Content-type', 'application/json');
    }

    public function handleBulkSmsNotificationAction()
    {
        // TODO - finish notification handling
        $handle = new \Samurai\BulkSms\Handle\NotificationHandle();

        if ($handle->isValid()) {

            $resource = $handle->resource();
            $status   = $resource->getStatus();
            $inputData = [];
            $smsId         = $resource->getSmsId();
            
            if ($status->isDelivered()) {
                
                $inputData['sms_sent'] = 1;
            }
            
            $statusCode = $status->getCode();
            $statusMessage = $status->getMessage();
            
            $inputData['sms_status_code'] = $statusCode;
            $inputData['sms_status_message'] = $statusMessage;
            
            // TODO Update model()->where('sms_id' => $smsId)->save($inputData);
            $id = $smsId;
            $data = array('sms_status_message'=>$statusMessage,'sms_sent'=>$inputData['sms_sent']);
            $model = Mage::getModel('easywebshopsms/easywebshopsms')->load($id)->addData($data);
            
                $model->setId($id)->save();
            // Update
        } else {
            // Invalid handle request
        }
    }

}