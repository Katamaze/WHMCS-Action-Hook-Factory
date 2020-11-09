<?php

/**
 * Client Group Color in Ticket View
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AdminAreaHeaderOutput', 1, function($vars)
{
    foreach (Capsule::select(Capsule::raw('SELECT t1.id, t2.groupcolour AS color FROM tblclients AS t1 LEFT JOIN tblclientgroups AS t2 ON t1.groupid = t2.id WHERE groupid != "0"')) as $v)
    {
        $users[$v->id] = $v->color;
    }

    $users = json_encode($users);

    return <<<HTML
<script type="text/javascript">
$(document).on('ready', function() {
    var data = {$users};
console.log(data);
    $("table#sortabletbl2 tr:has(td)").each(function () {
        var href = $(this).find('td:nth-child(5) a[href^="clientssummary.php?userid="]');

        if (typeof href !== undefined)
        {
            var params = $(href).attr('href').split('=');
            var id = params[params.length - 1];

            if (typeof data[id] !== 'undefined') {
                $(this).find('a[href^="clientssummary.php?userid="]').css('background-color', data[id]);
            }
        }
    });
});
</script>
HTML;
});
