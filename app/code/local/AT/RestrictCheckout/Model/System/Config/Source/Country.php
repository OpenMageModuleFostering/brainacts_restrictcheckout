<?php

class AT_RestrictCheckout_Model_System_Config_Source_Country
{
    protected $_options;

    public function toOptionArray()
    {

        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
        }

        $options = $this->_options;

        array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));


        return $options;
    }
}
