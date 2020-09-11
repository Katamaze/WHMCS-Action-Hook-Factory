<?php

/**
 * Assign Client to Group based on registration date
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
    // https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/README.md#client-to-group-based-on-registration-date
    $groups['1'] = '90';
    $groups['2'] = '180';
    $groups['3'] = '365';

    $activeCustomers = true;
    $oldestPurchase = 10;
    $ignoreDomains = false;
    $ignoreProducts = array('1');

    if (!$groups): return; endif;

    $filterStatus = ($activeCustomers ? ' AND status = "Active"' : false);

    if ($oldestPurchase)
    {
        $ignoreProducts = ($ignoreProducts ? ' AND t2.packageid NOT IN (\'' . implode('\',\'', $ignoreProducts) . '\')' : false);

        if ($ignoreDomains)
        {
            foreach (Capsule::select(Capsule::raw('SELECT t1.id FROM tblclients AS t1 LEFT JOIN tblhosting AS t2 ON t1.id = t2.userid WHERE DATEDIFF(CURDATE(), t2.regdate) >= "' . $oldestPurchase . '" '. $ignoreProducts .' GROUP BY t1.id')) as $v)
            {
                $filterPurchase[] = $v->id;
            }
        }
        else
        {
            foreach (Capsule::select(Capsule::raw('SELECT t1.id FROM tblclients AS t1 LEFT JOIN tblhosting AS t2 ON t1.id = t2.userid LEFT JOIN tbldomains AS t3 ON t1.id = t3.userid WHERE (DATEDIFF(CURDATE(), t2.regdate) >= "' . $oldestPurchase . '" '. $ignoreProducts .') OR DATEDIFF(CURDATE(), t3.registrationdate) >= "' . $oldestPurchase . '" GROUP BY t1.id')) as $v)
            {
                $filterPurchase[] = $v->id;
            }
        }

        if ($filterPurchase)
        {
            $filterPurchase = ' AND id IN (\'' . implode('\',\'', $filterPurchase) . '\')';
        }
    }

    foreach ($groups as $groupID => $days)
    {
        Capsule::table('tblclients')->whereRaw('DATEDIFF(CURDATE(), datecreated) >= "' . $days . '"' . $filterStatus . $filterPurchase)->update(['groupid' => $groupID]);
    }
});
