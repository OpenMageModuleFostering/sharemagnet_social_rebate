<?php
  class Sharemagnet_Social_Rebate_Model_Order extends Mage_Sales_Model_Order
  {
      /**  
        *   
        *   Resource name: rebate_session_order
            Request method: PUT
            Endpoint URL: http://stg.sharemagnet.com/rest/v1/social-rebates/rebate_session_order/<order_id>/
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
             $restApiKey = $this->getRestApiKey();
             $restApiSecret = $this->getRestApiSecret();
             $restApiMagnet = $this->getRestMagnetKey();
             if ($restApiKey !="" && $restApiSecret !="" & $restApiMagnet!="")
             {
                 $client1 =new Zend_Http_Client();
                   
                 // $UrI=$this->_requestSharemagnetApiUrl.$orderId."/";
                 // $client1->setConfig(array('adapter'=> 'Zend_Http_Client_Adapter_Curl'));
                 $client1->setUri($this->_verifySharemagnetApiUrl.$this->getId().'/?api_key='.$restApiKey.'&api_secret='.$restApiSecret.'&magnet_key='.$restApiMagnet.'&backend=magento');
                 
                                  
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
