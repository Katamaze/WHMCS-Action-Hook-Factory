<?php

/**
 * Set New Clients as Affiliates
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('ClientAreaRegister', 1, function($vars)
{
    $adminUsername = 'ADMIN_USERNAME'; // Optional for WHMCS 7.2 and later
    $results = localAPI('AffiliateActivate', array('userid' => $vars['userid']), $adminUsername);
});
