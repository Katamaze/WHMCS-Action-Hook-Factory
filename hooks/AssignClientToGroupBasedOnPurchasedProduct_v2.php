<?php

/**
 * Assign Client to Group based on purchased Product/Service v2
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    // Define group/product pairs. Instructions provided below
    // https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/README.md#client-to-group-based-on-purchased-productservice
    $groups['1'] = array('1', '2', '3');
    $groups['2'] = array('51');

    if ($groups AND in_array($vars['messagename'], array('CodeGuard Welcome Email', 'Dedicated/VPS Server Welcome Email', 'Hosting Account Welcome Email', 'Marketgoo Welcome Email', 'Other Product/Service Welcome Email', 'Reseller Account Welcome Email', 'SHOUTcast Welcome Email', 'SiteLock VPN Welcome Email', 'SiteLock Welcome Email', 'SpamExperts Welcome Email', 'Weebly Welcome Email')))
    {
        $orderedProducts = Capsule::table('tblhosting')->where('orderid', $vars['mergefields']['service_order_id'])->pluck('packageid');

        foreach ($groups as $group => $packages)
        {
            if (array_intersect($orderedProducts, $packages))
            {
                Capsule::table('tblclients')->where('id', $vars['mergefields']['client_id'])->where('groupid', '0')->update(['groupid' => $group]);
                $groupName = Capsule::table('tblclientgroups')->where('id', $group)->pluck('groupname')[0];
                $merge_fields['client_group_id'] = $group;
                $merge_fields['client_group_name'] = $groupName;
                break;
            }
        }

        return $merge_fields;
    }
});
