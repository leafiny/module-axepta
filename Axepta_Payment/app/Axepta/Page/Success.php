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
 * Class Axepta_Page_Success
 */
class Axepta_Page_Success extends Core_Page
{
    /**
     * Payment is successful
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        /** @var Axepta_Model_Payment_Online_Axepta $payment */
        $payment = App::getSingleton('model', Axepta_Model_Payment_Online_Axepta::PAYMENT_METHOD);

        $sale = $payment->getRequestedSale();
        if (!$sale) {
            $this->redirect();
        }

        $this->setCustom('key', $sale->getData('key'));
        $this->setCustom('result', Axepta_Helper_Axepta::PAYMENT_RESULT_SUCCESS);
    }
}
