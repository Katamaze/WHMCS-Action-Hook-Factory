<?php

/**
 * Simulate / Run WHMCS Daily Cron Job on demand
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AdminAreaHeaderOutput', 1, function($vars)
{
    if ($_GET['simulatecron'])
    {
        $SystemURL = Capsule::table('tblconfiguration')->where('setting', 'SystemURL')->first(['value'])->value;
        Capsule::table('tblconfiguration')->where('setting', 'lastDailyCronInvocationTime')->update(['value' => '']);
        Capsule::table('tblconfiguration')->where('setting', 'DailyCronExecutionHour')->update(['value' => date('H')]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $SystemURL . 'crons/cron.php');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    return <<<HTML
<style>
.katademo1 { color:#fff !important; background-color:#eaae53 !important;}
</style>
<script type="text/javascript">
$(document).on('ready', function(){
    $('body>.topbar>.pull-left').append('<a href="index.php?simulatecron=1" title="Each press simulates the Daily Cron of WHMCS" class="update-now katademo1" id="simulatingcronjob">Run Daily CronJob <i class="fa"></i></a><a href="index.php?reinstalldemo=1" title="Reinstall Demo with new sets of data" class="update-now katademo3" id="reinstallingdemo">Reinstall <i class="fa"></i></a><a href="https://katamaze.com/demo" target="_blank" title="Extend or End your Demo" class="update-now katademo2">Manage Demo</a>');
    $("#simulatingcronjob").on('click', function(e){
        if (confirm('Simulating the Cron Job might take a few minutes especially the very first time. Please be patient.'))
        {
            $(e.currentTarget).find('.fa').addClass('fa-pulse fa-spinner');
        }
	else
	{
	    e.preventDefault();
	}
    });
});
</script>
HTML;
});
