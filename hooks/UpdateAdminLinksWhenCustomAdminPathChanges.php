<?php

/**
 * Knowledgebase Author
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;
use WHMCS\Config\Setting;

define('KtConfigurationCustomAdminPath', $config->customadminpath);

add_hook('AdminAreaPage', 1, function($vars) {

    // Custom Admin Path not set. Nothing to do
    if (!KtConfigurationCustomAdminPath) {

        return;
    }

    $stored_custom_admin_path = Setting::getValue('KtStoredCustomAdminPath');

    // Storing current Custom Admin Path in tblconfigurations table to detect when it changes
    if (empty($stored_custom_admin_path)) {

        Setting::setValue('KtStoredCustomAdminPath', KtConfigurationCustomAdminPath);
        return;
    }

    // The stored and live version of Custom Admin Path (the one in configuration.php) are the same. Nothing to do
    if (KtConfigurationCustomAdminPath == $stored_custom_admin_path) {

        return;
    }

    // If we're here it means Custom Admin Path has been updated so we need to perform a couple of replacements
    $system_url = Setting::getValue('SystemURL');
    $find = $system_url . '/' . Setting::getValue('KtStoredCustomAdminPath');
    $replace = $system_url . '/' . KtConfigurationCustomAdminPath;

    Capsule::table('tbladmins')->update([ 'notes' => Capsule::raw('REPLACE(notes, "' . $find . '", "' . $replace . '")') ]);
    Capsule::table('tblnotes')->update([ 'note' => Capsule::raw('REPLACE(note, "' . $find . '", "' . $replace . '")') ]);
    Capsule::table('tblticketnotes')->update([ 'message' => Capsule::raw('REPLACE(`message`, "' . $find . '", "' . $replace . '")') ]);
    Capsule::table('tbltodolist')->update([ 'description' => Capsule::raw('REPLACE(`description`, "' . $find . '", "' . $replace . '")') ]);
    Setting::setValue('KtStoredCustomAdminPath', KtConfigurationCustomAdminPath);
});
