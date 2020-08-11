<?php

/**
 * Ticket Feedback on Auto Close
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 */
 
add_hook('EmailPreSend', 1, function($vars)
{
    if ($vars['messagename'] == 'Support Ticket Auto Close Notification' AND $vars['relid'])
    {
        $adminUsername = '';
        $results = localAPI('SendEmail', array('messagename' => 'Support Ticket Feedback Request', 'id' => $vars['relid']), $adminUsername);
    }
});
