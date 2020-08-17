<?php

/**
 * Promotions array in Email Templates
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    $emailTemplates = array('Invoice Payment Confirmation'); // Array of Email Templates in which you want to include Promotions array

    if (in_array($vars['messagename'], $emailTemplates))
    {
        return array('promotions' => json_decode(json_encode(Capsule::table('tblpromotions')->get()), true));
    }
});
