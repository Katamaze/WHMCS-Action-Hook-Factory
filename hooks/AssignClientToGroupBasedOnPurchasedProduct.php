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
    // Define group/product pairs. Instructions provided below
    // https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/README.md#client-to-group-based-on-purchased-productservice
    $groups['1'] = array('1', '2', '3');
    $groups['2'] = array('4');

    $userID = Capsule::table('tblorders')->where('id', $vars['orderid'])->pluck('userid')[0];
    $orderedProducts = Capsule::table('tblhosting')->where('orderid', $vars['orderid'])->pluck('packageid');

    foreach ($groups as $group => $packages)
    {
        if (array_intersect($orderedProducts, $packages))
        {
            Capsule::table('tblclients')->where('id', $userID)->where('groupid', '0')->update(['groupid' => $group]);
        }
    }
});
