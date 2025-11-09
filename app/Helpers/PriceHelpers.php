<?php

namespace App\Helpers;

class PriceHelpers
{
    /**
     * Calculate net price (without VAT) from a price that includes VAT.
     */
    public static function netFromVat(float $priceWithVat, float $vatRate): float
    {
        if ($vatRate <= 0) {
            return $priceWithVat;
        }

        return round($priceWithVat / (1 + ($vatRate / 100)), 2);
    }

    /**
     * Calculate price including VAT from a net price and VAT rate.
     */
    public static function vatFromNet(float $priceWithoutVat, float $vatRate): float
    {
        if ($vatRate <= 0) {
            return $priceWithoutVat;
        }

        return round($priceWithoutVat * (1 + ($vatRate / 100)), 2);
    }

    /**
     * Calculate VAT amount from a price that includes VAT.
     */
    public static function vatAmountFromVat(float $priceWithVat, float $vatRate): float
    {
        if ($vatRate <= 0) {
            return 0.0;
        }

        $net = self::netFromVat($priceWithVat, $vatRate);
        return round($priceWithVat - $net, 2);
    }

    /**
     * Calculate VAT amount from a net price.
     */
    public static function vatAmountFromNet(float $priceWithoutVat, float $vatRate): float
    {
        if ($vatRate <= 0) {
            return 0.0;
        }

        return round($priceWithoutVat * ($vatRate / 100), 2);
    }
}
