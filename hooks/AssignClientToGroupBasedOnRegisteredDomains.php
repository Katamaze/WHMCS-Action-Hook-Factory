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
    $placeholderGroup = '1';

    if (!$groups): return; endif;
    $defaultGroup = ($placeholderGroup ? $placeholderGroup : '0');

    $filterStatus = ($activeCustomers ? ' AND t2.status = "Active"' : false);
    $filterGroup = ($placeholderGroup ? ' AND (t2.groupid = "' . $placeholderGroup . '" OR t2.groupid IN (\'' . implode('\', \'', array_keys($groups)) . '\'))' : false);

    foreach (Capsule::select(Capsule::raw('SELECT t1.userid, COUNT(t1.id) as total, t2.groupid FROM tbldomains AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.status IN ("Active", "Grace", "Redemption") ' . $filterStatus . ' GROUP BY t1.userid')) as $v)
    {
        $current[$v->userid] = $v;
        $users[$v->userid] = $defaultGroup;

        foreach ($groups as $gid => $total)
        {
            if ($v->total >= $total)
            {
                $users[$v->userid] = $gid;
            }
        }
    }

    foreach (Capsule::table('tblclientgroups')->select('id', 'groupname')->get() as $v)
    {
        $groupLabels[$v->id] = $v->groupname;
    }

    foreach ($users as $userID => $groupID)
    {
        if ($current[$userID]->groupid != $groupID)
        {
            logActivity('Client Group Modified - User ID: ' . $userID . ' now has ' . $current[$userID]->total . ' domain(s) - Moved from #' . $current[$userID]->groupid . ' ' . $groupLabels[$current[$userID]->groupid] . ' to #' . $groupID . ' ' . $groupLabels[$groupID]);
        }

        Capsule::table('tblclients')->where('id', $userID)->update(['groupid' => $groupID]);
    }
});
