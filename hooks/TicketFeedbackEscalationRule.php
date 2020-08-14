<?php

/**
 * Ticket Feedback on Auto Close via Escalation Rule
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 */

use WHMCS\Database\Capsule;

add_hook('AfterCronJob', 1, function($vars)
{
    $cronFrequency = '5';
    $adminUsername = '';

    if ($cronFrequency)
    {
        $ticketLog = Capsule::select(Capsule::raw('SELECT t1.tid, t2.userid, SUBSTRING_INDEX(SUBSTRING_INDEX(t1.action, " \"", -1), "\" ", 1) as escalationrule FROM tblticketlog AS t1 LEFT JOIN tbltickets AS t2 ON t1.tid = t2.id WHERE t1.date >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 5 MINUTE), "%Y-%m-%d %H:%i:00") AND t1.action LIKE ("Escalation Rule \"%%\" applied") GROUP BY t1.tid'));
        $ticketLog = json_decode(json_encode($ticketLog), true);
        $escalationRules = Capsule::table('tblticketescalations')->where('newstatus', 'Closed')->pluck('name');

        foreach ($ticketLog as $v)
        {
            $key = array_search($v['escalationrule'], $escalationRules);

            if ($key)
            {
                $preventFeebackBouncing = Capsule::table('tblactivitylog')->where('userid', $v['userid'])->where('desription', 'LIKE', 'Support Ticket Feedback Request Sent %')->pluck('id');

                if (!$preventFeebackBouncing)
                {
                    logActivity('Support Ticket Feedback Request Sent (Escalation Rule: ' . $escalationRules[$key] . ') - Ticket ID: ' . $v['tid'] . ' - User ID: ' . $v['userid'], $v['userid']);
                    localAPI('SendEmail', array('messagename' => 'Support Ticket Feedback Request', 'id' => $v['tid']), $adminUsername);
                }
            }
        }
    }
});
