<?php

class AT_RestrictCheckout_Block_Rewrite_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
    /**
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {

        /** @var AT_RestrictCheckout_Helper_Data $helper */
        $helper = $this->helper('restrictcheckout');

        if ($helper->isEnabled()) {
            $allow = $helper->validate();

            if (!$allow) {
                return false;
            }
        }
        return parent::isPossibleOnepageCheckout();

    }
}
