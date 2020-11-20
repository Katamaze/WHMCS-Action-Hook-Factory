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
    // Define department/product pairs. Instructions provided below
    // https://github.com/Katamaze/WHMCS-Free-Scripts/blob/master/README.md#conditional-support-departments
    $department['1'] = array('1', '46', '47'); // The access to support department #1 (the ['1'] between square brackets) is restricted to users with product ID #45, #45 and #47
    $department['2'] = array('12', '13', '14');

    if ($vars['filename'] != 'submitticket'): return; endif;
    if (!$department): return; endif;
    $departmentIDs = array_keys($department);

    foreach (Capsule::select(Capsule::raw('SELECT packageid FROM tblhosting WHERE userid = "' . $vars['clientsdetails']['userid'] . '" AND domainstatus NOT IN ("Pending", "Suspended", "Terminated", "Cancelled", "Fraud") GROUP BY packageid')) as $v)
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
