<?php

/**
 * Assign Client to Group based on purchased Product/Service
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AcceptOrder', 1, function($vars)
{
    $productID  = 12; // Replace with the ID of your Product/Service
    $groupID    = 1; // Replace with the ID of your Client Group
    $userID     = Capsule::table('tblorders')->leftJoin('tblhosting', 'tblorders.id', '=', 'tblhosting.orderid')->where([['tblorders.id', '=', $vars['orderid']], ['tblhosting.packageid', '=', $productID]])->first(['tblorders.userid']);

    if ($userID)
    {
        Capsule::table('tblclients')->where('id', $userID->userid)->update(['groupid' => $groupID]);
    }
});
