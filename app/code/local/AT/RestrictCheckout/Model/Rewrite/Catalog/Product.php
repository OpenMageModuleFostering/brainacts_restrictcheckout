<?php

class AT_RestrictCheckout_Model_Rewrite_Catalog_Product extends Mage_Catalog_Model_Product
{



    /**
     * Check is product available for sale
     *
     * @return bool
     */
    public function isSalable()
    {
        /** @var AT_RestrictCheckout_Helper_Data $helper */
        $helper = Mage::helper('restrictcheckout');

        if ($helper->isEnabled()) {
            $allow = $helper->validate();

            if (!$allow) {
                return $allow;
            }
        }

        return parent::isSalable();

    }
}
