<?php

/**
 * Remove Portal Home breadcrumb
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('ClientAreaPage', 1, function($vars)
{
	unset($vars['breadcrumb'][0]);
	return array('breadcrumb' => $vars['breadcrumb']);
});
