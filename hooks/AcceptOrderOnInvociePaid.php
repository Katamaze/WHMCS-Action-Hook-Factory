<?php

/**
 * Automatically Accept Order when invoice is Paid
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('InvoicePaid', 1, function($vars)
{
    $orderID = Capsule::table('tblorders')->where('invoiceid', '=', $vars['invoiceid'])->pluck('id')[0];
    if (!$orderID): return; endif;

    $invoiceTotal = false; // Auto-accept order based on invoice total. The script performs currency conversion automatically. Leave false to auto-accept everything
    $operator = '<='; // Use ">=" to auto-accept orders greater than or equal to $invoiceTotal. Use "<=" for less than or equal to $invoiceTotal

    if ($invoiceTotal)
    {
        $currency = Capsule::select(Capsule::raw('SELECT t3.rate FROM tblinvoices AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id LEFT JOIN tblcurrencies AS t3 ON t2.currency = t3.id WHERE t1.id = "' . $vars['invoiceid'] . '" AND t3.default = "0" LIMIT 1'))[0];
        $invoiceTotal = ($currency ? $invoiceTotal * $currency->rate : $invoiceTotal);

        if (Capsule::table('tblinvoices')->where('id', '=', $vars['invoiceid'])->where('total', ($operator == '>=' ? '<=' : '>='), $invoiceTotal)->pluck('id')[0]): echo 'NON FACCIO L\'AUTO ACCEPT'; return; endif;
    }

    $adminUsername = ''; // Optional for WHMCS 7.2 and later
    localAPI('AcceptOrder', array('orderid' => $orderID), $adminUsername);
});
