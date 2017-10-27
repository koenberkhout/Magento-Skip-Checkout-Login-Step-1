<?php
/**
* Skip Checkout Login
* https://github.com/koenberkhout/Magento-Skip-Checkout-Login-Step-1
*
* @author    Koen Berkhout
* @copyright (c) Koen Berkhout - All rights reserved.
*/

class Mage_Adminhtml_Model_System_Config_Source_Skipcheckoutloginoptions
{

    public function toOptionArray()
    {
        return array(
		
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Register')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Guest')),
            
        );
    }

}