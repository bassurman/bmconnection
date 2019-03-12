<?php
require_once Mage::getBaseDir('lib').'/Billmate/utf8.php';

class Billmate_Connection_Helper_Data extends Mage_Core_Helper_Abstract
{
    const BM_CREDENTIALS_ID_PATH = 'payment/bm_connnection/eid';

    const BM_CREDENTIALS_SECRET_PATH = 'payment/bm_connnection/secret';

    const BM_CREDENTIALS_TEST_MODE_PATH = 'payment/bm_connnection/testmode';

    const BM_CREDENTIALS_DEBUG_MODE_PATH = 'payment/bm_connnection/testmode';

    protected $defPaymentData = [
        "currency"=> 'SEK',
        "country"=> 'se',
        "language"=> 'sv',
    ];

    /**
     * @param $path
     *
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return Mage::getStoreConfig($path);
    }

    /**
     * @return BillMate
     */
    public function getBmProvider()
    {
        $lang = explode('_', $this->getConfigValue('general/locale/code'));
        if (!defined('BILLMATE_LANGUAGE')) {
            define('BILLMATE_LANGUAGE', $lang[0]);
        }

        $eid = $this->getConnectionId();
        $secret = $this->getConnectionSecret();
        $testMode = $this->isTestMode();
        $debugMode = $this->isDebugMode();

        return $this->createProvider($eid, $secret, $testMode, $debugMode);
    }

    /**
     * @param $eid
     * @param $secret
     */
    public function verifyCredentials($eid, $secret)
    {
        $billmate = $this->createProvider($eid, $secret, false, false);
        $additionalInfo['PaymentData'] = $this->getDefPaymentData();

        $response = $billmate->GetPaymentPlans($additionalInfo);
        if (isset($response['code'])) {
            return false;
        }
        return true;
    }

    /**
     * @return int
     */
    public function getConnectionId()
    {
        return $this->getConfigValue(self::BM_CREDENTIALS_ID_PATH);
    }

    /**
     * @return string
     */
    public function getConnectionSecret()
    {
        return $this->getConfigValue(self::BM_CREDENTIALS_SECRET_PATH);
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return (bool)$this->getConfigValue(self::BM_CREDENTIALS_TEST_MODE_PATH);
    }

    /**
     * @return bool
     */
    public function isDebugMode()
    {
        return (bool)$this->getConfigValue(self::BM_CREDENTIALS_DEBUG_MODE_PATH);
    }

    /**
     * @param      $eid
     * @param      $secret
     * @param      $testMode
     * @param      $debugMode
     * @param bool $useSsl
     *
     * @return Billmate_Billmate
     */
    protected function createProvider($eid, $secret, $testMode, $debugMode, $useSsl = true)
    {
        return new Billmate_Billmate($eid, $secret, $useSsl, $testMode, $debugMode);
    }


    /**
     * @return array
     */
    protected function getDefPaymentData()
    {
        return $this->defPaymentData;
    }
}