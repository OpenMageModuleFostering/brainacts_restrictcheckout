<?php

class AT_RestrictCheckout_Helper_Rewrite_Checkout_Data extends Mage_Checkout_Helper_Data
{


    /**
     * Get onepage checkout availability
     *
     * @return bool
     */
    public function canOnepageCheckout()
    {
        /** @var AT_RestrictCheckout_Helper_Data $helper */
        $helper = Mage::helper('restrictcheckout');

        if ($helper->isEnabled()) {
            $allow = $helper->validate();

            if (!$allow) {
                return false;
            }
        }

        return parent::canOnepageCheckout();
    }


}
