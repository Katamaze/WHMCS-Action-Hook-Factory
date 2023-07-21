<?php

/**
 * Login as Client Language
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('AdminAreaHeaderOutput', 1, function($vars)
{
    if ($vars['filename'] == 'clientssummary' AND $_GET['userid'])
    {
        return <<<HTML
<script type="text/javascript">
$(document).on('ready', function(){

    href = $("#summary-login-as-client").attr('href');

    if (typeof href !== "undefined") {

        $("#summary-login-as-client").attr('href', href.replace(/&?language=\w+/, ''));
    }

    href = $("#summary-login-as-owner").attr('href');

    if (typeof href !== "undefined") {

        $("#summary-login-as-owner").attr('href', href.replace(/&?language=\w+/, ''));

        href = $("#summary-login-as-owner-new-window").attr('href');
        $("#summary-login-as-owner-new-window").attr('href', href.replace(/&?language=\w+/, ''));
    }
});
</script>
HTML;
    }
});
