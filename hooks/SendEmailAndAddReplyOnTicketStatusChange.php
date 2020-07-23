<?php

/**
 * Send Email & Add Reply on Ticket status change
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('TicketStatusChange', 1, function($vars)
{
    $adminUsername = 'admin'; // The reply will be added by this Admin user. Set false to open the ticket using your own customer
    $userID = Capsule::table('tbltickets')->where('id', $vars['ticketid'])->first(['userid'])->userid;

    // Email notification
    $EmailData = array(
        'id' => $userID,
        'customtype' => 'general',
        'customsubject' => 'Thank you for contacting us',
        'custommessage' => 'Your ticket status has been changed to ' .$vars['status']
    );

    localAPI('SendEmail', $EmailData);

    // Ticket reply
    $TicketData = array(
        'ticketid' => $vars['ticketid'],
        'message' => 'Your ticket status has been changed to ' .$vars['status'],
        'clientid' => $userID,
        'adminusername' => $adminUsername,
    );

    localAPI('AddTicketReply', $TicketData);
});
