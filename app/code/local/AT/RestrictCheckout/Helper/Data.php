<?php

class AT_RestrictCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_ENABLED = 'checkout/restriction/enabled';

    const XML_PATH_PERMANENT = 'checkout/restriction/permanent';

    const XML_PATH_GUEST = 'checkout/restriction/guest';

    const XML_PATH_WEBSITE = 'checkout/restriction/website';

    const XML_PATH_RANGES = 'checkout/restriction/zone';

    const XML_PATH_COUNTRY = 'checkout/restriction/country';

    const XML_PATH_MESSAGE_TYPE = 'checkout/restriction/message_type';

    const XML_PATH_MESSAGE = 'checkout/restriction/message';

    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function validate()
    {

        $status = Mage::registry('checkoutRestrict');

        if ($status != null) {
            if ($status == 'deny') {
                return false;
            } else {
                return true;
            }
        }


        if ($this->isPermanentBlocked()) {
            $this->setFlagRegistry('deny');
            return false;
        }

        if ($this->isGuestBlocked()) {
            $this->setFlagRegistry('deny');
            return false;
        }

        if ($this->isWebsiteBlocked()) {
            $this->setFlagRegistry('deny');
            return false;
        }

        if ($this->isIpBlocked()) {
            $this->setFlagRegistry('deny');
            return false;
        }

        if ($this->isCountryBlocked()) {

            $this->setFlagRegistry('deny');

            return false;
        }


        $this->setFlagRegistry('allow');
        return true;
    }

    /**
     * @return bool
     */
    protected function isPermanentBlocked()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_PERMANENT, Mage::app()->getStore()->getId());
    }

    /**
     * @return bool
     */
    protected function isGuestBlocked()
    {

        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            return false;
        }

        $status = (bool)Mage::getStoreConfig(self::XML_PATH_GUEST, Mage::app()->getStore()->getId());

        if ($status) {
            return true;
        }

        return false;

    }


    /**
     * @return bool
     */
    protected function isWebsiteBlocked()
    {
        $website = Mage::getStoreConfig(self::XML_PATH_WEBSITE, Mage::app()->getStore()->getId());
        if (empty($website)) {
            return false;
        }
        $websites = explode(',', $website);

        if (is_array($websites) && in_array(Mage::app()->getStore()->getWebsiteId(), $websites)) {
            return true;
        } else {
            if (Mage::app()->getStore()->getWebsiteId() == $websites) {
                return true;
            }
        }

        return false;
    }

    protected function isIpBlocked()
    {
        /** @var Mage_Core_Helper_Http $helper */
        $helper = Mage::helper('core/http');
        $ip = $helper->getRemoteAddr(false);
        $ip = ip2long($ip);

        $ranges = $this->getRanges();
        if (!$ranges) {
            return false;
        }
        foreach ($ranges as $item) {

            if ($ip >= $item['low'] && $ip <= $item['high']) {
                return true;
            }

        }
    }

    private function getRanges()
    {

        $data = Mage::getStoreConfig(self::XML_PATH_RANGES, Mage::app()->getStore()->getId());
        $data = trim($data);
        if (empty($data)) {
            return false;
        }
        $rangeList = explode(';', $data);

        $result = array();
        foreach ($rangeList as $rangeItem) {
            $resultItem = array();
            $items = explode('-', $rangeItem);
            if (isset($items[1])) {
                $resultItem['low'] = ip2long($items[0]);
                $resultItem['high'] = ip2long($items[1]);
            }
            $result[] = $resultItem;
        }


        return $result;
    }


    public function isCountryBlocked()
    {

        $countries = Mage::getStoreConfig(self::XML_PATH_COUNTRY, Mage::app()->getStore()->getId());

        if (empty($countries)) {
            return false;
        }

        $countries = explode(',', $countries);


        /** @var Mage_Core_Helper_Http $helper */
        $helper = Mage::helper('core/http');
        $ip = $helper->getRemoteAddr(false);

        $wrapper = new AT_MaxMind_GeoIP();
        $path = Mage::getBaseDir() . '/var/maxmind/GeoIP.dat';

        if (!file_exists($path)){
            return false;
        }
        $gi = $wrapper->geoip_open($path, GEOIP_STANDARD);

        $code = $wrapper->geoip_country_code_by_addr($gi, $ip);
        $code = strtoupper($code);
        if (empty($code) || $code == null) {
            return false;
        }

        $wrapper->geoip_close();


        if (is_array($countries) && in_array($code, $countries)) {
            return true;
        } else {
            if ($code == $countries) {
                return true;
            }
        }

        return false;

    }


    public function setFlagRegistry($newStatus)
    {
        Mage::unregister('checkoutRestrict');
        Mage::register('checkoutRestrict', $newStatus);


        $type = Mage::getStoreConfig(self::XML_PATH_MESSAGE_TYPE, Mage::app()->getStore()->getId());
        if ($type == 1 && $newStatus == 'deny') {
            /** @var Mage_Core_Model_Session $session */
            $session = Mage::getSingleton('core/session');
            $session->addNotice(Mage::getStoreConfig(self::XML_PATH_MESSAGE, Mage::app()->getStore()->getId()));
        }
    }

}