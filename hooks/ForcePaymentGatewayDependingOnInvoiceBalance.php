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

define('kt_gateway', 'banktransfer'); // Force this payment gateway when invoice balance is >= $limit. Use System Name (eg. banktransfer, paypal)
define('kt_limit', '0'); // Specifiy the limit in WHMCS Default Currency. The hook automatically handles currency conversion (0 to disable)
define('kt_countries', array()); // Optionally define countries where you want to apply this hook. Use ISO 3166-1 alpha-2 country codes (eg. IT, FR, US)
define('kt_europe', true); // Set true to use the hook on EU-based customers. This option can be used together with $countries

add_hook('ClientAreaPage', 100, function($vars) {

    if ($vars['filename'] == 'viewinvoice' AND $_GET['id']) {

        if (kt_countries AND !in_array($vars['clientsdetails']['countrycode'], kt_countries)): return; endif;
        if (kt_europe AND !in_array($vars['clientsdetails']['countrycode'], array('AT', 'BE', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FK', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IM', 'IT', 'LV', 'LT', 'LU', 'MT', 'MC', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'))): return; endif;

        if (kt_gateway AND kt_limit) {

            $currencyRate = Capsule::table('tblcurrencies')->where('id', '=', $vars['clientsdetails']['currency'])->where('default', '!=', '1')->pluck('rate')[0];
            $adminUnlock = Capsule::table('tblinvoices')->where('id', '=', $_GET['id'])->where('notes', 'like', '%Payment Method Unlocked by Administratror%')->pluck('notes')[0];
        }

        if ($adminUnlock) {

            $vars['notes'] = str_replace('Payment Method Unlocked by Administratror', '', $vars['notes']);

            if (!$vars['notes']) {

                return array('notes' => false);
            }
            else {

                return array('notes' => $vars['notes']);
            }
        }

        if ($currencyRate) {

            $balance = number_format($vars['balance']->toNumeric() / $currencyRate, 2, '.', '');
        }
        else {

            $balance = $vars['balance']->toNumeric();
        }

        if ($balance >= kt_limit AND !$adminUnlock) {

            if ($vars['paymentmodule'] == kt_gateway) {

                return array('allowchangegateway' => false);
            }
            else {

                Capsule::table('tblinvoices')->where('id', $_GET['id'])->update(['paymentmethod' => kt_gateway]);
                header('Location: viewinvoice.php?id=' . $_GET['id']);
                die();
            }
        }
    }
});

add_hook('ClientAreaHeadOutput', 1, function($vars) {

    if ($vars['filename'] == 'clientarea' AND $_GET['action'] == 'addfunds' AND kt_limit AND kt_gateway) {

        $limit = kt_limit;
        $gateway = kt_gateway;

        return <<<HTML
<script type="text/javascript">
$(document).ready(function() {

    var selectOptions = $('select[name="paymentmethod"]').html();

    $('input[name="amount"]').keyup(function() {

        var amount = $('input[name="amount"]').val();

        if(amount >= {$limit}) {

          $('select[name="paymentmethod"] option').each(function() {

            if ($(this).val() == '{$gateway}') {

                $(this).prop('selected', true);
            }
            else {

                $(this).remove();
            }
          });

        } else {

            $('select[name="paymentmethod"]').html(selectOptions);
        }
    });
});
</script>
HTML;
    }
});

add_hook('InvoiceChangeGateway', 1, function($vars) {

    if ($_SESSION['adminid']) {

        $currentNotes = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->pluck('notes')[0];
        $currentNotes = str_replace('Payment Method Unlocked by Administratror', '', $currentNotes);
        Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->update(['notes' => 'Payment Method Unlocked by Administratror' . $currentNotes]);
    }
});
