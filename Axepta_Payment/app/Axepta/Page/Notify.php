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
 * Class Axepta_Page_Notify
 */
class Axepta_Page_Notify extends Core_Page
{
    /**
     * The payment gateway webhook
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        try {
            /** @var Axepta_Model_Payment_Online_Axepta $payment */
            $payment = App::getSingleton('model', Axepta_Model_Payment_Online_Axepta::PAYMENT_METHOD);

            $sale = $payment->getRequestedSale('post');
            if (!$sale) {
                throw new Exception('Sale not found');
            }

            /** @var Axepta_Model_Payment_Axepta $axeptaPayment */
            $axeptaPayment = App::getSingleton('model', 'axepta');

            /** @var Commerce_Model_Sale $saleModel */
            $saleModel = App::getSingleton('model', 'sale');

            $sale->setData('payment_state', strtolower($axeptaPayment->getParam('Description')));
            $sale->setData('payment_ref', $axeptaPayment->getParam('PayID'));
            $saleModel->save($sale);

            /** @var Commerce_Model_Sale_History $historyModel */
            $historyModel = App::getSingleton('model', 'sale_history');
            $historyModel->save(
                new Leafiny_Object(
                    [
                        'sale_id'     => $sale->getData('sale_id'),
                        'status_code' => Commerce_Model_Sale_Status::SALE_STATUS_PENDING,
                        'language'    => $sale->getData('language'),
                        'comment'     => sprintf(
                            App::translate('The status of transaction %s is: %s'),
                            $axeptaPayment->getParam('PayID'),
                            strtoupper($axeptaPayment->getParam('Description'))
                        )
                    ]
                )
            );

            if ($axeptaPayment->isSuccessful()) {
                $sale->setData('status', Commerce_Model_Sale_Status::SALE_STATUS_PROCESSING);
                /** @var Commerce_Helper_Order $helper */
                $helper = App::getSingleton('helper', 'order');
                $helper->complete($sale);
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }
}
