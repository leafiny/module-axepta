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
 * Class Axepta_Page_Cancel
 */
class Axepta_Page_Cancel extends Core_Page
{
    /**
     * The payment was canceled by customer
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getParams();
        $key = $params->getData('key');

        if (!$key) {
            $this->redirect();
        }

        $this->setCustom('key', $key);
        $this->setCustom('result', Axepta_Helper_Axepta::PAYMENT_RESULT_CANCEL);
    }
}
