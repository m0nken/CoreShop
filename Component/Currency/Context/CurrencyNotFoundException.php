<?php

namespace CoreShop\Component\Currency\Context;

final class CurrencyNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Currency could not be found!', 0, $previousException);
    }

    /**
     * @param string $currencyCode
     *
     * @return self
     */
    public static function notFound($currencyCode)
    {
        return new self(sprintf('Currency "%s" cannot be found!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     *
     * @return self
     */
    public static function disabled($currencyCode)
    {
        return new self(sprintf('Currency "%s" is disabled!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     * @param array $availableCurrenciesCodes
     *
     * @return self
     */
    public static function notAvailable($currencyCode, array $availableCurrenciesCodes)
    {
        return new self(sprintf(
            'Currency "%s" is not available! The available ones are: "%s".',
            $currencyCode,
            implode('", "', $availableCurrenciesCodes)
        ));
    }
}
