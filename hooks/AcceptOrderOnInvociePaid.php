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

    $adminUsername = ''; // Optional for WHMCS 7.2 and later
    localAPI('AcceptOrder', array('orderid' => $orderID), $adminUsername);
});
