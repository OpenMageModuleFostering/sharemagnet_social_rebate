<?php
 
class Sharemagnet_Social_Rebate_Model_Observer
{
    /*
        Resource name: rebate_session
        Request method: POST
        Endpoint URL: http://sharemagnet.com/rest/v1/social-rebates/rebate_session/
        Mime type: application/json
    */
    protected $_initSharemagnetApiUrl = 'http://sharemagnet.com/rest/v1/social-rebates/rebate_session/';
    
    /*
        Resource name: rebate_session
        Request method: GET
        Endpoint URL: http://sharemagnet.com/rest/v1/social-rebates/rebate_session/<id>/
        GET params:

        magnet_key = ‘538441DA’
    */
    protected $_requestSharemagnetApiUrl = 'http://sharemagnet.com/rest/v1/social-rebates/rebate_session/';
    
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
    
    public function prepareService($event)
    {
        $order = $event->getOrder();
        $orderId= $order->getId();
        $orderEmail=$order->getCustomerEmail();
        $purchase_amount = $order->getGrandTotal() - $order->getShippingAmount();
        try 
        {
            $restApiKey = $this->getRestApiKey();
            $restApiSecret = $this->getRestApiSecret();
            $restApiMagnet = $this->getRestMagnetKey();
            
            if ($restApiKey !="" && $restApiSecret !="" & $restApiMagnet!="")
            {
                $client = new Zend_Http_Client();
                
                // initialize api session
                $client->setUri($this->_initSharemagnetApiUrl);
                
                
                $arr=array('magnet_key'=>$restApiMagnet,'type'=>'magento','order_id'=>$orderId,'order_email'=>$orderEmail,'total_purchase'=>$purchase_amount);
                $response=$client->setRawData(json_encode($arr), 'application/json')->request('POST');
                $call_integration_url=$response->getHeader("Location")."?magnet_key=".$restApiMagnet."&format=json";
                if ($response->getStatus()==201 && $response->getMessage()=="CREATED")
                {

                    //get existing api session 
                     $client1 =new Zend_Http_Client();
                   
                    $client1->setUri($response->getHeader("Location"));
                    $client1->setParameterGet(array('magnet_key'=>$restApiMagnet,'format'=>'json'));
                    
                    $response1 = $client1->request();
                    if ($response1->getStatus()==500 || $response1->getStatus()==501) 
                    {

                    }
                    else
                    {
                        $responseBody1 = json_decode($response1->getBody());
                        $integration_code=$responseBody1->integration_code;
                        $offer_type=$responseBody1->offer_type;
                        $order_id=$responseBody1->order_id;
                        $order_email=$responseBody1->order_email;
                        
                        Mage::getSingleton('core/session')->setIntegrationCode($integration_code);
                        /* */
                        Mage::getSingleton('core/session')->setOfferType($offer_type);
                    }
                }
                else
                {
                    
                }
            }
        } catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $responseBody = '';
        }
           
        return $this;
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