<?php

/**
 * Cancel Order when an Invoice is being Cancelled
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('InvoiceCancelled', 1, function($vars)
{
    $adminUsername = ''; // Optional for WHMCS 7.2 and later

    $orderID = Capsule::table('tblorders')->where('invoiceid', $vars['invoiceid'])->pluck('id')[0];

    if ($orderID)
    {
        localAPI('CancelOrder', array('orderid' => $orderID), $adminUsername);
    }
});
