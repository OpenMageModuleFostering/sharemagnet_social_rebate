<?php
  class Sharemagnet_Social_Rebate_Model_Order extends Mage_Sales_Model_Order
  {
      /**  
        *   
        *   Resource name: rebate_session_order
            Request method: PUT
            Endpoint URL: http://sharemagnet.com/rest/v1/social-rebates/rebate_session_order/<order_id>/
            GET params:

            api_key = ‘c7c43d18154e8fa317f0d0b8ae76d321f97fd221’
            api_secret = ‘0491c4c7f8e90ee93fd5b9e7be1b1d70a35374ca’
            magnet_key = ‘538441DA’
            backend = ‘magento’
            PUT params:

            order_verified (boolean)
        * 
        * 
        */
      protected $_verifySharemagnetApiUrl = 'http://sharemagnet.com/rest/v1/social-rebates/rebate_session_order/';
      
      protected function _beforeSave()
      {
         parent::_beforeSave();
         if ($this->getState()==Mage_Sales_Model_Order::STATE_PROCESSING) //STATE_PROCESSING
         {
             $order = $this;
             $restApiKey = $this->getRestApiKey();
             $restApiSecret = $this->getRestApiSecret();
             $restApiMagnet = $this->getRestMagnetKey();
             $orderId= $order->getId();
             $incrementId = $order->getIncremementId();
             $realOrderId=$order->getRealOrderId();
             if(!$orderId){
                $orderId = 'NONE';
            }
            if(!$realOrderId){
                $realOrderId = 'NONE';
            }
            if(!$incrementId){
                $incrementId = 'NONE';
            }
            try{
                $order_created = $order->created_at;
            }
            catch(Exception $e){
                $order_created = NULL;            
            }
            if(!$order_created){
                $order_created = 'NONE';
            }
             $orderEmail=$order->getCustomerEmail();
             $purchase_amount = $order->getGrandTotal() - $order->getShippingAmount();
             
             if ($restApiKey !="" && $restApiSecret !="" & $restApiMagnet!="")
             {
                 $client1 =new Zend_Http_Client();
                 $client1->setUri($this->_verifySharemagnetApiUrl.$this->getId().'/');
                 $client1->setParameterGet(array('api_key'=>$restApiKey,'api_secret'=>$restApiSecret,'magnet_key'=>$restApiMagnet,'type'=>'magento','backend'=>'magento','format'=>'json','order_id'=>$orderId,'increment_id'=>$incrementId,'real_order_id'=>$realOrderId,'order_email'=>$orderEmail,'total_purchase'=>$purchase_amount,'order_created'=>$order_created));
                 
                 $arr1=array('order_verified'=>1);
                 $response=$client1->setRawData(json_encode($arr1), 'application/json')->request(Zend_Http_Client::PUT);
                 
                 
             } 
         }

     }
     public function getRestApiKey() {
        return trim(Mage::getStoreConfig('social_rebate_section/social_rebate_group/social_rebate_apikey'));
    }

    public function getRestApiSecret() {
        return trim(Mage::getStoreConfig('social_rebate_section/social_rebate_group/social_rebate_apisecret'));
    }
    public function getRestMagnetKey() {
        return trim(Mage::getStoreConfig('social_rebate_section/social_rebate_group/social_rebate_magnetkey'));
    }
  }
?>
