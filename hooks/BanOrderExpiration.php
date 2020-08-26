<?php

/**
 * Ban Order Expiration
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Carbon;

add_hook('AdminAreaHeadOutput', 1, function($vars)
{
    if ($vars['filename'] == 'orders' AND $_GET['action'] == 'view' AND $_GET['id'])
    {
        $ban = Carbon::now();
        $ban->modify('next year');
        $year = $ban->format('Y');
        $month = $ban->format('m');
        $day = $ban->format('d');

        return <<<HTML
<script>
$(document).ready(function(){
    href = $('#contentarea table a[href^="configbannedips.php"]').attr('href');
    href = href.replace(/(year=)[0-9]+/, 'year={$year}');
    href = href.replace(/(month=)[0-9]+/, 'month={$month}');
    href = href.replace(/(day=)[0-9]+/, 'day={$day}');
    $('#contentarea table a[href^="configbannedips.php"]').attr('href', href);
})
</script>
HTML;
    }
});
