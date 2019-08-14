<?php

class Sharemagnet_Social_Rebate_Model_Observer{
    protected $_initSharemagnetApiUrl = 'http://sharemagnet.com/rest/v1/social-rebates/rebate_session/';
    protected $_requestSharemagnetApiUrl = 'http://sharemagnet.com/rest/v1/social-rebates/rebate_session/';
    protected $_verifySharemagnetApiUrl = 'http://sharemagnet.com/rest/v1/social-rebates/rebate_session_order/';
    
    public function prepareService($event){
        $order = $event->getOrder();
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
            $order_created = strftime('%Y-%m-%d %H:%M:%S',time());
        }
        $orderEmail=$order->getCustomerEmail();
        $purchase_amount = $order->getGrandTotal() - $order->getShippingAmount();
        try{
            $restApiKey = $this->getRestApiKey();
            $restApiSecret = $this->getRestApiSecret();
            $restApiMagnet = $this->getRestMagnetKey();
            if ($restApiKey !="" && $restApiSecret !="" & $restApiMagnet!=""){
                $client = new Zend_Http_Client();
                // initialize api session
                $client->setUri($this->_initSharemagnetApiUrl);
                $client->setConfig(array(
                            'timeout'      => 5));
                $arr=array('magnet_key'=>$restApiMagnet,'type'=>'magento','order_id'=>$orderId,'increment_id'=>$incrementId,'real_order_id'=>$realOrderId,'order_email'=>$orderEmail,'total_purchase'=>$purchase_amount,'order_created'=>$order_created);
                $response=$client->setRawData(json_encode($arr), 'application/json')->request('POST');
                $call_integration_url=$response->getHeader("Location")."?magnet_key=".$restApiMagnet."&format=json";
                $call_integration_url .= "&real_order_id=" . $realOrderId . "&increment_id=" . $incrementId . "&order_created=" . $order_created . "&order_email=" . $orderEmail. "&purchase_amount=" . $purchase_amount;
                if ($response->getStatus()==201 && $response->getMessage()=="CREATED"){
                    //get existing api session 
                    $client1 =new Zend_Http_Client();
                    $client1->setUri($response->getHeader("Location"));
                    $client1->setConfig(array(
                            'timeout'      => 5));
                    $client1->setParameterGet(array('magnet_key'=>$restApiMagnet,'type'=>'magento','format'=>'json','order_id'=>$orderId,'increment_id'=>$incrementId,'real_order_id'=>$realOrderId,'order_email'=>$orderEmail,'total_purchase'=>$purchase_amount,'order_created'=>$order_created));
                    $response1 = $client1->request();
                    if($response1->getStatus()!=500 && $response1->getStatus()!=501){
                        $responseBody1 = json_decode($response1->getBody());
                        $integration_code=$responseBody1->integration_code;
                        $offer_type=$responseBody1->offer_type;
                        $order_id=$responseBody1->order_id;
                        $order_email=$responseBody1->order_email;
                        Mage::getSingleton('core/session')->setIntegrationCode($integration_code);
                        Mage::getSingleton('core/session')->setOfferType($offer_type);
                    }
                }
            }
        }catch(Exception $e){
            #$debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            #$responseBody = '';
        }
        return $this;
    }

    public function getRestApiKey(){
        return trim(Mage::getStoreConfig('social_rebate_section/social_rebate_group/social_rebate_apikey'));
    }

    public function getRestApiSecret(){
        return trim(Mage::getStoreConfig('social_rebate_section/social_rebate_group/social_rebate_apisecret'));
    }

    public function getRestMagnetKey(){
        return trim(Mage::getStoreConfig('social_rebate_section/social_rebate_group/social_rebate_magnetkey'));
    }  

    public function verifyOrderToSharemagnet($event){
        try{
        $order = $event->getOrder();
            if ($order->getState()==Mage_Sales_Model_Order::STATE_PROCESSING){
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
                if ($restApiKey !="" && $restApiSecret !="" & $restApiMagnet!=""){
                    $client1 =new Zend_Http_Client();
                    $client1->setUri($this->_verifySharemagnetApiUrl.$order->getId().'/');
                    $client1->setConfig(array(
                            'timeout'      => 5));
                    $client1->setParameterGet(array('api_key'=>$restApiKey,'api_secret'=>$restApiSecret,'magnet_key'=>$restApiMagnet,'type'=>'magento','backend'=>'magento','format'=>'json','order_id'=>$orderId,'increment_id'=>$incrementId,'real_order_id'=>$realOrderId,'order_email'=>$orderEmail,'total_purchase'=>$purchase_amount,'order_created'=>$order_created));
                    $arr1=array('order_verified'=>1);
                    $response=$client1->setRawData(json_encode($arr1), 'application/json')->request(Zend_Http_Client::PUT);
                }
            }
        }catch(Exception $e){}
    }
}
?>
