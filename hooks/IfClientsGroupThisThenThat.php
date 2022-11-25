<?php

/**
 * If client's group this then that
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

define('kt_groups', [

    // Rules for client group id `1` (visit `configclientgroups.php`)
    1 => [

        'tax_exempt' => true, // `true` = enable tax exempt | `false` = disable tax exempt | `null` = don't cange. Changes to tax exempt status only apply to new invoices. Existing ones are left unchanged
        'invoice_header' => 'Ferrari S.p.A.' . PHP_EOL . 'Via Emilia Est 1163' . PHP_EOL . '41121 Modena - Italia', // Replaces the `Pay to` section of invoices with this one. Set `false` to leave default details. IMPORTANT! This does't affect `invoicepdf.tpl` file (the PDF version of invoice). This file can't be accessed via hooks. You'll need to put an if/else statement yourself
        'allowed_payment_gateways' => [ // Array of payment gateways that clients from this group are allowed to use. Leave empty for no restriction

            'paypalcheckout', // The first gateway in the array is always used as default when it comes to replacing restricted ones from open invoices (not in `Paid`, `Collections`, `Refund`, `Payment Pending` status)
            'banktransfer'
        ]
    ],
    // Rules for client group id `2` (visit `configclientgroups.php`)
    2 => [

        'tax_exempt' => false,
        'invoice_header' => 'Juventus S.p.A.' . PHP_EOL . 'Via Druento 175' . PHP_EOL . '10151 Torino - Italia',
        'allowed_payment_gateways' => [

            'banktransfer'
        ]
    ]
]);

// If the above array is empty there's no need to go on with the script. It ends here
if (!kt_groups) {

    return;
}

function kt_LoadCompanySettings($client_id)
{
    // Retreive client group id
    $client_group_id = Capsule::table('tblclients')->where('id', $client_id)->whereIn('id', array_keys(kt_groups))->pluck('groupid')[0];

    // Client has no group or there is no custom rule defined for his group. We can exit
    if (!$client_group_id OR !isset(kt_groups[$client_group_id])) {

        return;
    }

    $settings = kt_groups[$client_group_id];

    // We have restrictions to payment gateways to take care of
    if ($settings['allowed_payment_gateways']) {

        // List of all payment gateways of WHMCS
        $payment_gateways = Capsule::table('tblpaymentgateways')->where('setting', 'name')->pluck('gateway')->toArray();

        // Calculate the difference between allowed gateways and available ones. In essence the array contains the list of gatways that should be restricted
        $restricted_gateways = array_diff($payment_gateways, $settings['allowed_payment_gateways']);

        // We found at least one restricted gateway
        if ($restricted_gateways) {

            $settings['restricted_gateways'] = $restricted_gateways;
        }
    }

    // Array that contains all custom settings defined in kt_groups for the selected group/client
    return [
     
        'client_id' => $client_id,
        'settings' => $settings
    ];
}

function kt_UpdateClient($data = null)
{
    // No data. There's nothing to do on the client in question. Exiting...
    if (!$data) {

        return;
    }

    // Updating tax exempt status of client depending on company's settings
    if ($data['settings']['tax_exempt'] === true) {

        $tax_exempt = 1;
    }
    elseif ($data['settings']['tax_exempt'] === false) {

        $tax_exempt = 0;
    }

    // We appy change only if `tax_exempt` was set in `kt_group` as `true` or `false`
    if (isset($tax_exempt)) {

        Capsule::table('tblclients')->where('id', $data['client_id'])->update(['taxexempt' => $tax_exempt]);
    }

    // Removing restricted gateway(s) from open invoices (not in `Paid`, `Collections`, `Refunded`, `Payment Pending` status) of current client. Restricted gatewaty get replaced with the first gateway defined in `kt_groups.allowed_payment_gateways`
    if ($data['settings']['restricted_gateways']) {

        Capsule::table('tblinvoices')->where('userid', $data['client_id'])->whereNotIn('status', [ 'Paid', 'Collections', 'Refunded', 'Payment Pending' ])->whereIn('paymentmethod', $data['settings']['restricted_gateways'])->update(['paymentmethod' => $data['settings']['allowed_payment_gateways'][0]]);
    }
}

// Apply `kt_groups` settings as a client is being added to WHMCS 
add_hook('ClientAdd', 1, function($vars) {

    $data = kt_LoadCompanySettings($vars['client_id']);
    kt_UpdateClient($data);
});

// Apply `kt_groups` settings when a client is edited through the client area, admin area or API
add_hook('ClientEdit', 1, function($vars) {

    $data = kt_LoadCompanySettings($vars['userid']);
    kt_UpdateClient($data);
});

// Apply `kt_groups` settings when a client is viewing an invoice
add_hook('ClientAreaPageViewInvoice', 1, function($vars) {

    $data = kt_LoadCompanySettings($vars['userid']);

    // No changes to apply to `viewinvoice.php`
    if (!isset($data['settings']['restricted_gateways']) AND !$data['settings']['invoice_header']) {

        return;
    }

    // At least one payment gateway is restricted to the client
    if ($data['settings']['restricted_gateways']) {

        // Parsing `$vars['gatewaydropdown']` as HTML (the variable contains the HTML of `Payment method` dropdown accessible from `viewinvoice.php`)
        $dom = new DOMDocument();
        $dom->loadHTML($vars['gatewaydropdown']);

        // I feed DOM to xPath in order to access `<option></option>` tags of the `<select></select>` as array
        $xpath = new DomXPath($dom);

        // Prepare array to store xPath select conditions. I need this to tell xPath that for example I don't want `paypal` and `banktransfer` in the dropdown
        $xpath_conditions = [];

        // Looping every restricted payment gateway
        foreach ($data['settings']['restricted_gateways'] as $v) {

            $xpath_conditions[] = '@value="' . $v . '"';
        }

        // Imploding conditions by `" or "` as xPath is expecting
        $xpath_conditions = implode($xpath_conditions, ' or ');

        // Looping every `<option></option>` of restricted payment gateways
        foreach($xpath->query('//select/option[(' . $xpath_conditions . ')]') as $node) {

            // Removing the restricted payment gateway from dropdown
            $node->parentNode->removeChild($node);
        }

        // Overriding default WHMCS dropdown with mine
        $vars['gatewaydropdown'] = $dom->saveXml();
    }

    // `Pay to` needs to be changed
    if ($data['settings']['invoice_header']) {

        $vars['payto'] = nl2br($data['settings']['invoice_header']);
    }

    return [

        'gatewaydropdown' => $vars['gatewaydropdown'],
        'payto' => $vars['payto']
    ];
});
