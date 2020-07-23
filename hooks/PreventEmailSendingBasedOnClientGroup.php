<?php

/**
 * Prevent emails to be send based on Client Group
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    $disallowedGroupIDs = array('1', '2'); // Array of Client Group ID to block
    $emailTemplates = array('Automated Password Reset', 'Password Reset Validation', 'Password Reset Confirmation'); // Email Templates to block (General Messages)

    if (in_array($vars['messagename'], $emailTemplates))
    {
        if (!Capsule::select(Capsule::raw('SELECT id FROM tblclients WHERE id = "' . $vars['relid'] . '" AND groupid IN (\'' . implode('\',\'', $disallowedGroupIDs) . '\') LIMIT 1')))
        {
            $output['abortsend'] = true;
            return $output;
        }
    }
});
