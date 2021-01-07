<?php

/**
 * Plesk Checker
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 */

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

function PleskChecker_config()
{
	$configarray = array(
		"name" => "Plesk Checker",
	    	"description" => 'Check for missing integrations between WHMCS Hosting Accounts and Plesk Servers',
		"version" => "1.0.0",
		"author" => "<a href=\"http://katamaze.com\" target=\"_blank\" title=\"katamaze.com\"><img src=\"../modules/addons/PleskChecker/images/katamaze.png\"></a>",
		"fields" => array());

	return $configarray;
}

function PleskChecker_activate()
{

}

function PleskChecker_deactivate()
{

}

function PleskChecker_upgrade($vars)
{

}

function PleskChecker_output($vars)
{
	$smarty = new Smarty();
	$smarty->caching = false;
	$smarty->compile_dir = $GLOBALS['templates_compiledir'];
	$smarty->setTemplateDir(array(0 => '../modules/addons/PleskChecker/templates/Admin'));

	require_once('core/Katamaze/Checker.php');
	$checker = new Checker();
	
	$smarty->assign('checker', $checker->Plesk());
    	$smarty->display(dirname(__FILE__) . '/templates/Admin/Main.tpl');
}
