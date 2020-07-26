<?php

/**
 * Stronger Password Generator for WHMCS Provisioning v3
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('PreModuleCreate', 1, function($vars)
{
    $length['digit']    = '3'; // Number of digits in password
    $length['lower']    = '4'; // Number of UNIQUE lowercase characters in password
    $length['upper']    = '4'; // Number of UNIQUE uppercase characters in password
    $length['special']  = '2'; // Number of special characters in password

    // The same character cannot be used more than once (case sensitive)
    if ($length['lower'] + $length['upper'] == '26')
    {
        $length['lower'] = '13';
        $length['upper'] = '13';
    }

    $digits             = '0123456789';
    $chars              = 'abcdefghijklmnopqrstuvwxyz';
    $special            = '!@#$%^&*?'; // Plesk does not consider (, ), -, = and + as special characters
    $digits             = substr(str_shuffle($digits), 0, $length['digit']);
    $lower              = substr(str_shuffle($chars), 0, $length['lower']);
    $upper              = substr(str_shuffle(strtoupper(str_replace(str_split($lower), '', $chars) )), 0, $length['lower']);
    $special            = substr(str_shuffle($special), 0, $length['special']);
    $password           = str_shuffle($digits . $lower . $upper . $special);

    Capsule::table('tblhosting')->where('id', $vars['params']['serviceid'])->update(['password' => Encrypt($password)]);
});
