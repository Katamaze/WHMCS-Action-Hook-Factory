<?php

/**
 * Change Default Sorting of Tables in Backend
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

// How to use it: https://github.com/Katamaze/WHMCS-Action-Hook-Factory/blob/master/README.md#change-default-sorting-of-tables-in-backend

add_hook('AdminAreaPage', 1, function($vars) {

    if ($vars['filename'] == 'invoices' AND !$_COOKIE['WHMCSSD']) {

        setcookie('WHMCSSD', base64_encode(json_encode(array('invoices' => array('orderby' => 'date', 'sort' => 'DESC')))), time() + (86400 * 30), '/');
        header('Location: invoices.php');
        die();
    }
});
