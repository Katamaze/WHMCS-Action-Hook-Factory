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

add_hook('TicketStatusChange', 1, function($vars) {

    $adminUsername = 'admin'; // The reply will be added by this Admin user. Set false to open the ticket using your own customer
    $ticketDetails = Capsule::table('tbltickets')->where('id', $vars['ticketid'])->first(['userid', 'tid', 'title']);

    // Email notification
    $EmailData = array(
        'id' => $ticketDetails->userid,
        'customtype' => 'general',
        'customsubject' => $ticketDetails->title. ' Changed to ' . strtoupper($vars['status']) . ' [Ticket ID #' . $ticketDetails->tid . ']',
        'custommessage' => 'Your ticket status has been changed to ' .$vars['status']
    );

    localAPI('SendEmail', $EmailData);

    // Ticket reply
    $TicketData = array(
        'ticketid' => $vars['ticketid'],
        'message' => $ticketDetails->title. ' Changed to ' . strtoupper($vars['status']) . ' [Ticket ID #' . $ticketDetails->tid . ']',
        'clientid' => $userID,
        'adminusername' => $adminUsername,
    );

    localAPI('AddTicketReply', $TicketData);
});
