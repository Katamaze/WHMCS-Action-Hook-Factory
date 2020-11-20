<?php

/**
 * Conditional Support Departments
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('ClientAreaPage', 1, function($vars)
{
    if ($vars['filename'] != 'submitticket'): return; endif;

    $department['1'] = array('45', '46', '47'); // The access to support department #1 (the ['1'] between square brackets) is restricted to users with product ID #45, #46 and #47
    $department['2'] = array('12', '13', '14');

    if (!$department): return; endif;
    $departmentIDs = array_keys($department);

    foreach (Capsule::select(Capsule::raw('SELECT packageid FROM tblhosting WHERE userid = "' . $vars['clientsdetails']['userid'] . '" GROUP BY packageid')) as $v)
    {
        $packageIDs[] = $v->packageid;
    }

    foreach ($vars['departments'] as $k => $v)
    {
        if (!in_array($v['id'], $departmentIDs))
        {
            $overrideDepartments[$k] = $vars['departments'][$k];
            $allowedDepartments[] = $v['id'];
        }
        else
        {
            if (!array_intersect($department[$v['id']], $packageIDs))
            {
                $overrideDepartments[$k] = $vars['departments'][$k];
                $allowedDepartments[] = $v['id'];
            }
        }
    }

    if ($_GET['deptid'] AND !in_array($_GET['deptid'], $allowedDepartments)): header('Location: submitticket.php'); die(); endif;

    return array('departments' => $overrideDepartments);
});
