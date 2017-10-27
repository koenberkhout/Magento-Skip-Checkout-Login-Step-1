<?php
/**
* Skip Checkout Login
* https://github.com/koenberkhout/Magento-Skip-Checkout-Login-Step-1
*
* @author    Koen Berkhout
* @copyright (c) Koen Berkhout - All rights reserved.
*/

require_once "Mage/Checkout/controllers/OnepageController.php";
if (!Mage::getStoreConfig('skipcheckoutlogin_section/skipcheckoutlogin_group/skipcheckoutlogin_enable')) {
    class KoenBerkhout_SkipCheckoutLogin_Checkout_OnepageController extends Mage_Checkout_OnepageController {}
} else {
    
    class KoenBerkhout_SkipCheckoutLogin_Checkout_OnepageController extends Mage_Checkout_OnepageController {

        public function postDispatch()
        {
            parent::postDispatch();
            Mage::dispatchEvent('controller_action_postdispatch_adminhtml', array('controller_action' => $this));
        }
        
        public function indexAction()
        {
            if (!Mage::helper('checkout')->canOnepageCheckout()) {
                Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
                $this->_redirect('checkout/cart');
                return;
            }
            $quote = $this->getOnepage()->getQuote();
            if (!$quote->hasItems() || $quote->getHasError()) {
                $this->_redirect('checkout/cart');
                return;
            }
            if (!$quote->validateMinimumAmount()) {
                $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                    Mage::getStoreConfig('sales/minimum_order/error_message') :
                    Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

                Mage::getSingleton('checkout/session')->addError($error);
                $this->_redirect('checkout/cart');
                return;
            }
            Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));
            $this->getOnepage()->initCheckout();
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
            
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                $skipCheckoutLoginType = Mage::getStoreConfig('skipcheckoutlogin_section/skipcheckoutlogin_group/skipcheckoutlogin_type');
                // 1 => always register
                // 2 => always checkout as guest
                $method = ($skipCheckoutLoginType == 1) ? 'register' : 'guest';
                $this->getOnepage()->saveCheckoutMethod($method);
            }
            
            $this->renderLayout();
        }
        
        public function saveMethodAction()
        {
            if ($this->_expireAjax()) {
                return;
            }
            
            $skipCheckoutLoginType = Mage::getStoreConfig('skipcheckoutlogin_section/skipcheckoutlogin_group/skipcheckoutlogin_type');
            // 1 => always register
            // 2 => always checkout as guest

            if ($this->getRequest()->isPost()) {
                $method = ($skipCheckoutLoginType == 1) ? 'register' : 'guest';
                $result = $this->getOnepage()->saveCheckoutMethod($method);
                $this->_prepareDataJSON($result);
            }
        }

    }
    
}