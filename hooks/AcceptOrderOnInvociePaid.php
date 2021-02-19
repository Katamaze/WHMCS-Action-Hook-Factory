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

add_hook('InvoicePaid', 1, function($vars) {

    $orderID = Capsule::table('tblorders')->where('invoiceid', '=', $vars['invoiceid'])->pluck('id')[0];
    if (!$orderID): return; endif;

    $invoiceTotal = '10'; // Auto-accept order based on invoice total. The script automatically performs currency conversion. Leave false to auto-accept everything
    $operator = '<='; // Use "<=" to auto-accept orders less than or equal to $invoiceTotal. Use ">=" for the opposite

    if ($invoiceTotal) {

        $currency = Capsule::select(Capsule::raw('SELECT t3.rate FROM tblinvoices AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id LEFT JOIN tblcurrencies AS t3 ON t2.currency = t3.id WHERE t1.id = "' . $vars['invoiceid'] . '" AND t3.default = "0" LIMIT 1'))[0];
        $invoiceTotal = ($currency ? $invoiceTotal * $currency->rate : $invoiceTotal);

        if (Capsule::select(Capsule::raw('SELECT id FROM tblinvoices WHERE id = "' . $vars['invoiceid'] . '" AND (total ' . ($operator == '>=' ? '<=' : '>=') . ' "' . $invoiceTotal . '" OR credit ' . ($operator == '>=' ? '<=' : '>=') . ' "' . $invoiceTotal . '") LIMIT 1'))[0]) {

            return;
        }
    }

    $adminUsername = ''; // Optional for WHMCS 7.2 and later
    localAPI('AcceptOrder', array('orderid' => $orderID), $adminUsername);
});

add_hook('AfterProductUpgrade', 1, function($vars) {

    $orderID = Capsule::table('tblupgrades')->where('id', '=', $vars['upgradeid'])->pluck('orderid')[0];
    if (!$orderID): return; endif;

    $invoiceTotal = '10'; // Auto-accept order based on invoice total. The script automatically performs currency conversion. Leave false to auto-accept everything
    $operator = '<='; // Use "<=" to auto-accept orders less than or equal to $invoiceTotal. Use ">=" for the opposite

    if ($invoiceTotal) {

        $currency = Capsule::select(Capsule::raw('SELECT t3.rate FROM tblinvoices AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id LEFT JOIN tblcurrencies AS t3 ON t2.currency = t3.id WHERE t1.id = "' . $vars['invoiceid'] . '" AND t3.default = "0" LIMIT 1'))[0];
        $invoiceTotal = ($currency ? $invoiceTotal * $currency->rate : $invoiceTotal);

        if (Capsule::select(Capsule::raw('SELECT t2.id FROM tblorders AS t1 LEFT JOIN tblinvoices AS t2 ON t1.invoiceid = t2.id WHERE t1.id = "' . $orderID . '" AND (t2.total ' . ($operator == '>=' ? '<=' : '>=') . ' "' . $invoiceTotal . '" OR t2.credit ' . ($operator == '>=' ? '<=' : '>=') . ' "' . $invoiceTotal . '") LIMIT 1'))[0]) {

            return;
        }
    }

    $adminUsername = ''; // Optional for WHMCS 7.2 and later
    localAPI('AcceptOrder', array('orderid' => $orderID), $adminUsername);
});
