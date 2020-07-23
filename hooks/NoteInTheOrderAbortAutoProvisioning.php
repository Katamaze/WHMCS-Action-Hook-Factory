<?php

/**
 * Abort auto-provisioning when there's a note in the order
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('PreModuleCreate', 1, function($vars)
{
    $Data = Capsule::select(Capsule::raw('SELECT t1.notes FROM tblorders AS t1 LEFT JOIN tblhosting AS t2 ON t1.id = t2.orderid WHERE t2.id = "' . $vars['params']['serviceid'] . '" AND t2.orderid != "0"'));

    if ($Data[0]->notes)
    {
        return array('abortcmd' => true);
    }
});
