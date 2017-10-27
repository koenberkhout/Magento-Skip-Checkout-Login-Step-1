<?php
/**
* Skip Checkout Login
* https://github.com/koenberkhout/Magento-Skip-Checkout-Login-Step-1
*
* @author    Koen Berkhout
* @copyright (c) Koen Berkhout - All rights reserved.
*/
if (!Mage::getStoreConfig('skipcheckoutlogin_section/skipcheckoutlogin_group/skipcheckoutlogin_enable')) {
    class KoenBerkhout_SkipCheckoutLogin_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage {}
} else {

    class KoenBerkhout_SkipCheckoutLogin_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
    {
        
        public function getSteps()
        {
            $steps = array();
            $stepCodes = $this->_getStepCodes();

            $stepCodes = array_diff($stepCodes, array('login'));

            foreach ($stepCodes as $step) {
                $steps[$step] = $this->getCheckout()->getStepData($step);
            }

            return $steps;
        }
        
        public function getActiveStep()
        {
            return 'billing';
        }
        
    }
    
}