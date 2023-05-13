<?php

/**
 * Remove IP address from View Ticket page in Client Area
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('ClientAreaPage', 1, function($vars) {
    
    if ($vars['templatefile'] == 'viewticket') {

        $output = [];

        foreach ($vars['ascreplies'] as $index => $replies) {

            foreach ($replies as $k => $v) {

                if ($k == 'ipaddress') {

                    $v = false;
                }

                $output['ascreplies'][$index][$k] = $v;
            }
        }

        foreach ($vars['descreplies'] as $index => $replies) {

            foreach ($replies as $k => $v) {

                if ($k == 'ipaddress') {

                    $v = false;
                }

                $output['descreplies'][$index][$k] = $v;
            }
        }

        return $output;
    }
});
