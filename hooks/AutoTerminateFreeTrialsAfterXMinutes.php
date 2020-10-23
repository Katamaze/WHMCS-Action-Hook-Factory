<?php

/**
 * Auto-Terminate (or Suspend) Free Trials After X Minutes (not one full day)
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AfterCronJob', 1, function($vars)
{
    $productIDs = array('1', '2'); // WHMCS Product IDs to terminate or suspend
    $terminateAfter = 5; // Terminate or suspend products after the given number of minutes (1440 = full day - 0 to disable)
    $performAction = 'Terminate'; // Terminate or Suspend
    $adminUsername = ''; // Optional for WHMCS 7.2 and later

    if ($productIDs AND $terminateAfter)
    {
        $date = new DateTime;
        $date = $date->format('Y-m-d H:i:s');

        if ($performAction == 'Terminate')
        {
            $moduleAction = 'ModuleTerminate';
            $domainStatus = 'Terminated';
        }
        elseif ($performAction == 'Suspend')
        {
            $moduleAction = 'ModuleSuspend';
            $domainStatus = 'Suspended';
        }
        else
        {
            return;
        }

        $orderIDs = Capsule::table('tblorders')->whereRaw('NOW() <= date + INTERVAL 1 DAY')->pluck('date', 'id');
        if (!$orderIDs): return; endif;
        $keys = array_keys($orderIDs);

        $hostingIDs = Capsule::table('tblhosting')->whereIn('orderid', $keys)->whereIn('packageid', $productIDs)->where('domainstatus', '!=', $domainStatus)->pluck('orderid', 'id');
        if (!$hostingIDs): return; endif;

        $limit = new DateTime();

        foreach ($hostingIDs as $k => $v)
        {
            $orderDate = new DateTime($orderIDs[$v]);
            $interval = $limit->diff($orderDate);
            $elapsed = $interval->format('%i');

            if ($elapsed >= $terminateAfter)
            {
                $response = localAPI($moduleAction, array('serviceid' => $k), $adminUsername);

                if ($response['message'] == 'Module Not Found')
                {
                    localAPI('UpdateClientProduct', array('serviceid' => $k, 'status' => $domainStatus), $adminUsername);
                }

                $log[] = 'Service ID: ' . $k;
            }
        }

        if ($log)
        {
            logActivity('Free Trial ' . $performAction . ': ' . implode(', ', $log));
        }
    }
});
