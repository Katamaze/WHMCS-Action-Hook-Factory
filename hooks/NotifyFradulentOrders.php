<?php

/**
 * Notify Fraudulent Orders
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('FraudOrder', 1, function($vars)
{
    $admins = Capsule::table('tbladmins')->where('disabled', '=', '0')->pluck('username');

    foreach ($admins as $username)
    {
        localAPI('SendAdminEmail', array('type' => 'system', 'customsubject' => 'Fraud Order Detected', 'custommessage' => 'Order #' . $vars['orderid'] . ' detected as Fraudulent'), $username);
    }
});
