<?php
/**
 * This file is part of Leafiny.
 *
 * Copyright (C) Magentix SARL
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Class Axepta_Model_Payment_Online_Axepta
 */
class Axepta_Model_Payment_Online_Axepta extends Payment_Model_Payment
{
    /**
     * @var string PAYMENT_METHOD
     */
    const PAYMENT_METHOD = 'axepta_cc';

    /**
     * Retrieve method name
     *
     * @return string
     */
    public function getMethod(): string
    {
        return self::PAYMENT_METHOD;
    }

    /**
     * Process payment
     *
     * @param Leafiny_Object $sale
     * @throws Exception|Throwable
     */
    public function processPayment(Leafiny_Object $sale): void
    {
        if (!$this->getMerchantId()) {
            throw new Exception('The Merchant ID is missing');
        }
        if (!$this->getHMACKey()) {
            throw new Exception('The HMAC key is missing');
        }
        if (!$this->getBlowfishKey()) {
            throw new Exception('The Blowfish key is missing');
        }

        /** @var Axepta_Model_Payment_Axepta $axeptaPayment */
        $axeptaPayment = App::getSingleton('model', 'axepta');
        /** @var Axepta_Helper_Axepta $axeptaHelper */
        $axeptaHelper = App::getSingleton('helper', 'axepta');

        $language  = substr($sale->getData('language'), 0, 2);

        $axeptaPayment->setSecretKey($this->getHMACKey());
        $axeptaPayment->setCryptKey($this->getBlowfishKey());
        $axeptaPayment->setUrl(Axepta_Model_Payment_Axepta::PAYSSL);
        $axeptaPayment->setMerchantId($this->getMerchantId());
        $axeptaPayment->setTransID('SALE_ID_' . $sale->getData('sale_id'));
        $axeptaPayment->setAmount($axeptaHelper->formatAmount($sale->getData('incl_tax_total')));
        $axeptaPayment->setCurrency($sale->getData('sale_currency'));
        $axeptaPayment->setRefNr($sale->getData('sale_id'));
        $axeptaPayment->setURLSuccess(App::getBaseUrl(true) . 'payment/axepta/success/');
        $axeptaPayment->setURLFailure(App::getBaseUrl(true) . 'payment/axepta/failure/');
        $axeptaPayment->setURLNotify(App::getBaseUrl(true) . 'payment/axepta/notify/');
        $axeptaPayment->setURLBack(App::getBaseUrl(true) . 'payment/axepta/cancel/?key=' . $sale->getData('key'));
        $axeptaPayment->setLanguage($language);
        $axeptaPayment->setOrderDesc($this->getOrderDesc());
        $axeptaPayment->setMsgVer();
        $axeptaPayment->setResponseParam();

        $axeptaPayment->validate();
        $axeptaPayment->getShaSign();

        $params = [
            'MerchantID'   => $this->getMerchantId(),
            'Data'         => $axeptaPayment->getBFishCrypt(),
            'Len'          => $axeptaPayment->getLen(),
            'URLBack'      => $axeptaPayment->getParam('URLBack'),
            'CustomField1' => $axeptaHelper->formatCurrency(
                $sale->getData('incl_tax_total'),
                $sale->getData('language'),
                $sale->getData('sale_currency')
            ),
            'CustomField2' => $this->getOrderDesc(),
        ];

        $paymentUrl = $axeptaPayment->getUrl() . '?' . http_build_query($params);

        $paymentData = json_decode($sale->getData('payment_data'), true);

        $paymentData['redirect'] = $paymentUrl;

        $sale->setData('status', Commerce_Model_Sale_Status::SALE_STATUS_PENDING_PAYMENT);
        $sale->setData('payment_title', App::translate($this->getTitle()));
        $sale->setData('payment_state', 'pending');
        $sale->setData('payment_data', json_encode($paymentData));
    }

    /**
     * Retrieve the sale
     *
     * @param string $method
     *
     * @return Leafiny_Object|false
     */
    public function getRequestedSale()
    {
        try {
            if (!$this->getMerchantId()) {
                throw new Exception('The Merchant ID is missing');
            }
            if (!$this->getHMACKey()) {
                throw new Exception('The HMAC key is missing');
            }
            if (!$this->getBlowfishKey()) {
                throw new Exception('The Blowfish key is missing');
            }

            /** @var Axepta_Model_Payment_Axepta $axeptaPayment */
            $axeptaPayment = App::getSingleton('model', 'axepta');
            $axeptaPayment->setSecretKey($this->getHMACKey());
            $axeptaPayment->setCryptKey($this->getBlowfishKey());

            $response = empty($_POST) ? $_GET : $_POST;
            if (empty($response)) {
                return false;
            }

            $axeptaPayment->setResponse($response);

            if (!$axeptaPayment->isValid()) {
                return false;
            }

            /** @var Commerce_Model_Sale $saleModel */
            $saleModel = App::getSingleton('model', 'sale');
            $sale = $saleModel->get((int)$axeptaPayment->getParam('refnr'));
            if (!$sale->getData('sale_id')) {
                return false;
            }

            return $sale;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return false;
    }

    /**
     * Retrieve API Key
     *
     * @return string|null
     */
    protected function getMerchantId(): ?string
    {
        return $this->getCustom('merchant_id');
    }

    /**
     * Retrieve API Key
     *
     * @return string|null
     */
    protected function getHMACKey(): ?string
    {
        return $this->getCustom('hmac_key');
    }

    /**
     * Retrieve API Key
     *
     * @return string|null
     */
    protected function getBlowfishKey(): ?string
    {
        return $this->getCustom('blowfish_key');
    }

    /**
     * Retrieve order description
     *
     * @return string|null
     */
    protected function getOrderDesc(): string
    {
        return App::translate($this->getCustom('order_desc'));
    }
}
