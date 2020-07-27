<?php

/**
 * Force Payment Gateway depending on Invoice Balance
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('ClientAreaPage', 1, function($vars)
{
    $gateway = 'banktransfer'; // Force this payment gateway when invoice balance is >= $limit. Use System Name (eg. banktransfer, paypal)
    $limit = '0'; // Specifiy the limit in WHMCS Default Currency. The hook automatically handles currency conversion (0 to disable)
    $countries = array(); // Optionally define countries where you want to apply this hook. Use ISO 3166-1 alpha-2 country codes (eg. IT, FR, US)
    $europe = false; // Set true to use the hook on EU-based customers. This option can be used together with $countries

    if ($vars['filename'] == 'viewinvoice' AND $_GET['id'])
    {
        if ($countries AND !in_array($vars['clientsdetails']['countrycode'], $countries)): return; endif;
        if ($europe AND !in_array($vars['clientsdetails']['countrycode'], array('AT', 'BE', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FK', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IM', 'IT', 'LV', 'LT', 'LU', 'MT', 'MC', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'))): return; endif;

        if ($gateway AND $limit)
        {
            $currencyRate = Capsule::table('tblcurrencies')->where('id', '=', $vars['clientsdetails']['currency'])->where('default', '!=', '1')->pluck('rate')[0];

            if ($currencyRate)
            {
                $balance = number_format($vars['balance']->toNumeric() / $currencyRate, 2, '.', '');
            }
            else
            {
                $balance = $vars['balance']->toNumeric();
            }

            if ($balance >= $limit)
            {
                if ($vars['paymentmodule'] == $gateway)
                {
                    return array('allowchangegateway' => false);
                }
                else
                {
                    Capsule::table('tblinvoices')->where('id', $_GET['id'])->update(['paymentmethod' => $gateway]);
                    header('Location: viewinvoice.php?id=' . $_GET['id']);
                    die();
                }
            }
        }
    }
});
