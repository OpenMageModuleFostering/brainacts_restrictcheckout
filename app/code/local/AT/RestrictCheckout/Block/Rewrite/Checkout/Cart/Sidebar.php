<?php

class AT_RestrictCheckout_Block_Rewrite_Checkout_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{


    /**
     * Check if one page checkout is available
     *
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
