<?php

/**
 * Abort the sending of email templates based on User ID and/or Client Group ID
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    $disallowedEmailTemplates = array('Invoice Created'); // The name of the email template being sent
    $disallowedClientGroups = array('3'); // Client Group ID
    $disallowedUserIDs = array('1'); // User ID

    switch (Capsule::table('tblemailtemplates')->select('type')->where('name', $vars['messagename'])->first()->type)
    {
        case 'affiliate': $user = $vars['relid']; break;
        case 'domain': $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid FROM tbldomains AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        case 'general': $user = $vars['relid']; break;
        case 'invoice': $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid FROM tblinvoices AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        case 'product': $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid FROM tblhosting AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        case 'support': $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid FROM tbltickets AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        default: return; break;
    }

    if (in_array($user->groupid, $disallowedClientGroups))
    {
        return array('abortsend' => true);
    }
    elseif (in_array($user->id, $disallowedUserIDs))
    {
        return array('abortsend' => true);
    }
});
