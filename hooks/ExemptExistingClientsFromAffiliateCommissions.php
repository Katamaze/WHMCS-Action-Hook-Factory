<?php

/**
 * Exempt existing clients from affiliate commissions
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AffiliateCommission', 1, function($vars)
{
    $numberOfDays = '10';

    if ($numberOfDays)
    {
        if (!Capsule::select(Capsule::raw('SELECT t1.id FROM tblhosting AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['serviceId'] . '" AND DATEDIFF(CURDATE(), t2.datecreated) <= "' . $numberOfDays . '"')))
        {
            $return['skipCommission'] = true;
            return $return;
        }
    }
});
