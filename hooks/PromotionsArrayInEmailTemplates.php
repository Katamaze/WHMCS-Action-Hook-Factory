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
        $promotions = json_decode(json_encode(Capsule::table('tblpromotions')->get()), true);

        foreach ($promotions as $k => $v)
        {
            if ($v['expirationdate'] != '0000-00-00' AND date('Y-m-d') >= $v['expirationdate'])
            {
                unset($promotions[$k]);
                continue;
            }

            if ($v['maxuses'] > '0' AND $v['uses'] == $v['maxuses'])
            {
                unset($promotions[$k]);
                continue;
            }
        }

        return array('promotions' => $promotions);
    }
});
