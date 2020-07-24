<?php

/**
 * Auto-Login to cPanel/Plesk from My Services
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

// IMPORTANT! The hook requires changes to two template files. Read the following for instructions
// https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/README.md#cpanel--plesk-login-button-in-my-services

use WHMCS\Database\Capsule;

add_hook('ClientAreaPage', 1, function($vars)
{
    if ($vars['filename'] == 'clientarea' AND $_GET['action'] == 'services' AND $_SESSION['uid'])
    {
        $productIDs = Capsule::select(Capsule::raw('SELECT t1.id, t2.type FROM tblhosting AS t1 LEFT JOIN tblservers AS t2 ON t1.server = t2.id WHERE t1.userid = ' . $_SESSION['uid'] . ' AND t1.server != "0" AND t1.domainstatus IN ("Active", "Suspended") AND t1.username IS NOT NULL AND t1.password IS NOT NULL AND t2.type IS NOT NULL'));

        foreach ($productIDs as $v)
        {
            $output['kt_autologin'][$v->id] = $v;
        }

        return $output;
    }
});

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    if ($vars['filename'] == 'clientarea' AND $_GET['action'] == 'productdetails' AND $_GET['id'] AND $_GET['autologin'])
    {
        return <<<HTML
<script type="text/javascript">
$(document).ready(function() {
    $("#domain form").removeAttr('target');
    $("#domain form").submit();
});
</script>
HTML;
    }
});
