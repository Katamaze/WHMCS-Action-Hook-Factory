<?php

/**
 * Assign Client to Group based on registered domains
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('DailyCronJob', 1, function($vars)
{
    // Define group/product pairs. Instructions provided below
    // https://github.com/Katamaze/WHMCS-Free-Action-Hooks#client-to-group-based-on-registered-domains
    $groups['1'] = '10';
    $groups['2'] = '25';
    $groups['3'] = '100';

    $activeCustomers = true;

    if (!$groups): return; endif;

    $filterStatus = ($activeCustomers ? ' AND t2.status = "Active"' : false);

    foreach (Capsule::select(Capsule::raw('SELECT t1.userid, COUNT(t1.id) as total FROM tbldomains AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.status IN ("Active", "Grace", "Redemption") ' . $filterStatus . ' GROUP BY t1.userid')) as $v)
    {
        $users[$v->userid] = '0';

        foreach ($groups as $gid => $total)
        {
            if ($v->total >= $total)
            {
                $users[$v->userid] = $gid;
            }
        }
    }

    foreach ($users as $userID => $groupID)
    {
        Capsule::table('tblclients')->where('id', $userID)->update(['groupid' => $groupID]);
    }
});
