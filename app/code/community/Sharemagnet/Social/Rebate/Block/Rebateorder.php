<?php
class Sharemagnet_Social_Rebate_Block_Rebateorder extends Mage_Checkout_Block_Onepage_Success {
    public function getOrder() {
        return Mage::getModel('sales/order')->loadByIncrementId($this->getOrderId());    
    }    
}
