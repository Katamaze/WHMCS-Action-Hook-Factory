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
    $v8 = Capsule::select(Capsule::raw('SELECT value FROM tblconfiguration WHERE setting = "Version" LIMIT 1'))[0]->value;
    if (explode('.', $v8)[0] != '8'): return; endif;

    $ordersTotal = Capsule::select(Capsule::raw('SELECT COUNT(t1.id) AS total FROM tblorders AS t1 LEFT JOIN tblorderstatuses AS t2 ON t1.status = t2.title WHERE t2.showpending = "1"'))[0]->total;
    $invoicesTotal = Capsule::select(Capsule::raw('SELECT COUNT(id) AS total FROM tblinvoices WHERE status = "Unpaid" AND duedate <= CURDATE()'))[0]->total;
    $ticketsTotal = Capsule::select(Capsule::raw('SELECT COUNT(t1.id) AS total FROM tbltickets AS t1 LEFT JOIN tblticketstatuses AS t2 ON t1.status = t2.title WHERE t2.showawaiting = "1" AND merged_ticket_id = "0"'))[0]->total;
    if (!$ordersTotal AND !$invoicesTotal AND !$ticketsTotal): return; endif;
	$notificationsLabel = AdminLang::trans('setup.notifications');
	$orderText = AdminLang::trans('stats.pendingorders');
	$invoiceText = AdminLang::trans('stats.overdueinvoices');
	$ticketText = AdminLang::trans('stats.ticketsawaitingreply');

	if ($ordersTotal)
    {
        $pendingOrdersJS = <<<HTML
        $('#v8fallback').next('ul').append('<li><a href="orders.php?status=Pending" data-toggle="tooltip" data-placement="bottom" title="{$orderText}" data-original-title="{$orderText}" style="word-wrap:break-word"><small><span class="ico-container"><i class="fad fa-shopping-cart"></i></span><span class="v8fallback">{$ordersTotal}</span> {$orderText}</small></a></li>');
HTML;
	}

	if ($invoicesTotal)
    {
        $overdueInvoicesJS = <<<HTML
        $('#v8fallback').next('ul').append('<li><a href="invoices.php?status=Overdue" data-toggle="tooltip" data-placement="bottom" title="{$invoiceText}" data-original-title="{$invoiceText}" style="word-wrap:break-word"><small><span class="ico-container"><i class="fad fa-sack-dollar"></i></span><span class="v8fallback">{$invoicesTotal}</span> {$invoiceText}</small></a></li>');
HTML;
	}

	if ($ticketsTotal)
	{
        $awaitingTicketsJS = <<<HTML
        $('#v8fallback').next('ul').append('<li><a href="supporttickets.php" data-toggle="tooltip" data-placement="bottom" title="{$ticketText}" data-original-title="{$ticketText}" style="word-wrap:break-word"><small><span class="ico-container"><i class="fad fa-question-circle"></i></span><span class="v8fallback">{$ticketsTotal}</span> {$ticketText}</small></a></li>');
HTML;
	}

	return <<<HTML
<script type="text/javascript">
$(document).on('ready', function() {
    $('ul.right-nav').first('li').prepend('<li class="bt has-dropdown"><a id="v8fallback" href="#"><div class="badge-container"><i class="fas fa-exclamation-triangle always"></i><span class="badge"><span class="fas fa-times"></span></span></div><span class="visible-sidebar">&nbsp;{$notificationsLabel}</span></a><ul class="drop-icons"></ul></li>');

    $("*[id=\'v8fallback\']").on("click", function(e) {
        e.preventDefault();
        $(e.currentTarget).parent("li").toggleClass("expanded");
    });

    {$pendingOrdersJS}
    {$overdueInvoicesJS}
    {$awaitingTicketsJS}

    $('#v8fallback').next('ul').css({"width": "340px", "left": "-134px"});
    $('span.v8fallback').css({"font-weight": "700"});
});
</script>
HTML;
});
