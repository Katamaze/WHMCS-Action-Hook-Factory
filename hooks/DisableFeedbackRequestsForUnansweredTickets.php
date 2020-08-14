<?php

/**
 * Disable Feedback Requests for Unanswered Tickets
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    if ($vars['messagename'] == 'Support Ticket Feedback Request')
    {
        if (!Capsule::table('tblticketreplies')->where('tid', $vars['relid'])->where('admin', '!=', '')->pluck('id')[0])
        {
            return array('abortsend' => true);
        }
    }
});
