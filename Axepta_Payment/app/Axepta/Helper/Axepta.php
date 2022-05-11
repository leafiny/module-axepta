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
 * Axepta_Helper_Axepta
 */
class Axepta_Helper_Axepta extends Core_Helper
{
    public const PAYMENT_RESULT_CANCEL = 'cancel';

    public const PAYMENT_RESULT_FAILURE = 'failure';

    public const PAYMENT_RESULT_SUCCESS = 'success';

    /**
     * Format amount
     *
     * @param mixed $amount
     *
     * @return int
     */
    public function formatAmount($amount): int
    {
        return (int)((float)$amount * 100);
    }

    /**
     * Format currency
     *
     * @param mixed       $value
     * @param string      $language
     * @param string|null $currency
     *
     * @return string
     */
    public function formatCurrency($value, string $language, ?string $currency = null): string
    {
        $value = (float)str_replace(',', '.', (string)$value);

        $formatter = new NumberFormatter(App::getLanguage(), NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($value, $currency) ?: '';
    }
}
