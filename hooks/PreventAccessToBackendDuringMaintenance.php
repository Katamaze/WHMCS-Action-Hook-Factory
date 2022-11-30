<?php

/**
 * Prevent Admins from accessing WHMCS backend during Maintenance
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

define('kt_maintenance_mode_allowed_admins', [  ]); // Array of Admin Ids that are allowed to access WHMCS backend when maintenance mode is enabled
define('kt_maintenance_mode_allowed_admin_roles', [ ]); // Array of Admin Role Ids that are allowed to access WHMCS backend when maintenance mode is enabled

// If the above arrays are empty there's no need to go on with the script. It ends here
if (!kt_maintenance_mode_allowed_admin_roles AND !kt_maintenance_mode_allowed_admins) {

    return;
}

add_hook('AdminAreaPage', 1, function($vars) {

    // Detect if Mainenance Mode is enabled or disabled
    $maintenance_mode = Capsule::table('tblconfiguration')->where('setting', 'MaintenanceMode')->pluck('value')[0];

    // Maintenance Mode is disabled. Exiting...
    if ($maintenance_mode != 'on') {

        return;
    }

    // `kt_maintenance_mode_allowed_admins` is set. Verify if the currently logged admin can access backend during maintenance
    if (kt_maintenance_mode_allowed_admins) {

        // Not allowed. Forcing logout...
        if (!in_array($_SESSION['adminid'], kt_maintenance_mode_allowed_admins)) {

            header('Location: logout.php?');
            die();
        }
    }

    // `kt_maintenance_mode_allowed_admin_roles` is set. Verify if the currently logged admin group can access backend during maintenance
    if (kt_maintenance_mode_allowed_admin_roles) {

        $admin_role_id = Capsule::table('tbladmins')->where('id', $_SESSION['adminid'])->pluck('roleid')[0];

        // Not allowed. Forcing logout...
        if (!in_array($admin_role_id, kt_maintenance_mode_allowed_admin_roles)) {

            header('Location: logout.php');
            die();
        }
    }
});
