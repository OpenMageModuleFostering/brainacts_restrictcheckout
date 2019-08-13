<?php

class AT_RestrictCheckout_Model_Observer
{
    public function validate()
    {

        /** @var AT_RestrictCheckout_Helper_Data $helper */
        $helper = Mage::helper('restrictcheckout');

        if ($helper->isEnabled()) {
            $helper->validate();
        }

    }

    public function refresh()
    {
        Mage::unregister('checkoutRestrict');
    }


    public function removePaypalButtons($observer)
    {

        /** @var AT_RestrictCheckout_Helper_Data $helper */
        $helper = Mage::helper('restrictcheckout');

        if ($helper->isEnabled()) {
            $allow = $helper->validate();

            if (!$allow) {
                /** @var Mage_Paypal_Block_Express_Shortcut $block */
                $block = Mage::app()->getLayout()->getBlock('checkout.cart.methods.paypal_express.top');

                if (null !== $block) {
                    $block->setTemplate('at/restrictcheckout/empty.phtml');
                }

                $block = Mage::app()->getLayout()->getBlock('checkout.cart.methods.paypal_express.bottom');

                if (null !== $block) {
                    $block->setTemplate('at/restrictcheckout/empty.phtml');
                }
            }
        }

        return $this;
    }

}