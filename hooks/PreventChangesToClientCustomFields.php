<?php

/**
 * Prevent changes to Client Custom Fields
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('CustomFieldSave', 1, function($vars)
{
    $ReadOnlyFields = array('1', '2'); // IDs of Custom Fields that cannot be edited
    $DisallowAdmin = false; // true = Even Administrators are not allowed to edit | false = Administrators can freely update Custom Fields

    /* Do not edit below */
    $IsAdmin = (basename($_SERVER['PHP_SELF']) == 'clientsprofile.php' ? true : false);

    if (in_array($vars['fieldid'], $ReadOnlyFields) AND (($IsAdmin AND $DisallowAdmin) OR (!$IsAdmin)))
    {
        return array('value' => Capsule::table('tblcustomfieldsvalues')->where(['fieldid' => $vars['fieldid'], 'relid' => $vars['relid']])->first(['value'])->value);
    }
});
