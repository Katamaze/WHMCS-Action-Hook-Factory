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

    if (!$groups): return; endif;

    foreach ($groups as $groupID => $days)
    {
        Capsule::table('tblclients')->whereRaw('DATEDIFF(CURDATE(), datecreated) >= "' . $days . '"')->update(['groupid' => $groupID]);
    }
});
