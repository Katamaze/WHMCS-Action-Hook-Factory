<?php

/**
 * Admin Stats for WHMCS v8
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AdminAreaHeaderOutput', 1, function($vars)
{
    if (explode('.', $vars['licenseinfo']['currentversion'])[0] != '8'): return; endif;
    $ordersTotal = Capsule::select(Capsule::raw('SELECT COUNT(t1.id) AS total FROM tblorders AS t1 LEFT JOIN tblorderstatuses AS t2 ON t1.status = t2.title WHERE t2.showpending = "1"'))[0]->total;
    $invoicesTotal = Capsule::select(Capsule::raw('SELECT COUNT(id) AS total FROM tblinvoices WHERE status = "Unpaid"'))[0]->total;
    $ticketsTotal = Capsule::select(Capsule::raw('SELECT COUNT(t1.id) AS total FROM tbltickets AS t1 LEFT JOIN tblticketstatuses AS t2 ON t1.status = t2.title WHERE t2.showawaiting = "1"'))[0]->total;
    if (!$ordersTotal AND !$invoicesTotal AND !$ticketsTotal): return; endif;
    $notificationsLabel = AdminLang::trans('setup.notifications');
    $ordersLabel = '<span class="v8fallback">' . $ordersTotal . '</span> ' . AdminLang::trans('stats.pendingorders');
    $invoicesLabel = '<span class="v8fallback">' . $invoicesTotal . '</span> ' . AdminLang::trans('stats.overdueinvoices');
    $ticketsLabel = '<span class="v8fallback">' . $ticketsTotal . '</span> ' . AdminLang::trans('stats.ticketsawaitingreply');

    return <<<HTML
<style>
a#v8fallback + ul.drop-icons {
    width: 350px
}
a#v8fallback + ul.drop-icons li a {
    //height: 100px
}
a#v8fallback + ul.drop-icons li a .v8fallback {
    font-weight: 700;
    //color: #1b4d7f
}
</style>
<script type="text/javascript">
$(document).on('ready', function() {
    $('ul.right-nav').first('li').prepend('<li class="bt has-dropdown"><a id="v8fallback" href="#"><div class="badge-container"><i class="fas fa-exclamation-triangle always"></i><span class="badge"><span class="fas fa-times"></span></span></div><span class="visible-sidebar">&nbsp;{$notificationsLabel}</span></a><ul class="drop-icons"></ul></li>');

    $("*[id=\'v8fallback\']").on("click", function(e) {
        e.preventDefault();
        $(e.currentTarget).parent("li").toggleClass("expanded");
    });

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    var orders = '<li><a href="orders.php?status=Pending" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{$hover}" style="word-wrap:break-word"><span class="ico-container"><i class="fad fa-shopping-cart"></i></span>{$ordersLabel}</a></li>';
    var invoices = '<li><a href="invoices.php?status=Overdue" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{$hover}" style="word-wrap:break-word"><span class="ico-container"><i class="fad fa-question-circle"></i></span>{$invoicesLabel}</a></li>';
    var tickets = '<li><a href="supporttickets.php" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{$hover}" style="word-wrap:break-word"><span class="ico-container"><i class="fad fa-sack-dollar"></i></span>{$ticketsLabel}</a></li>';
    $('#v8fallback').next('ul').append(orders.concat(invoices).concat(tickets));
});
</script>
HTML;
});
