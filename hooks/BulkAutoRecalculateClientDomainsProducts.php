<?php

/**
 * Bulk Auto Recalculate Client Domain & Products/Services
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 */

use WHMCS\Database\Capsule;

add_hook('AdminAreaHeaderOutput', 1, function($vars)
{
    if ($vars['filename'] == 'clientssummary' AND $_GET['userid'] AND in_array($_GET['kata'], array('bulkAutoRecalculateP', 'bulkAutoRecalculateD')))
    {
        $adminUsername = ''; // Optional for WHMCS 7.2 and later

        if ($_GET['kata'] == 'bulkAutoRecalculateP')
        {
            foreach(Capsule::table('tblhosting')->where('userid', '=', $_GET['userid'])->pluck('id') as $v)
            {
                localAPI('UpdateClientProduct', array('serviceid' => $v, 'autorecalc' => true), $adminUsername);
            }

            header('Location: clientssummary.php?userid=' . $_GET['userid']);
            die();
        }
        elseif ($_GET['kata'] == 'bulkAutoRecalculateD')
        {
            foreach (Capsule::table('tbldomains')->where('userid', '=', $_GET['userid'])->pluck('id') as $v)
            {
                localAPI('UpdateClientDomain', array('domainid' => $v, 'autorecalc' => true), $adminUsername);
            }

            header('Location: clientssummary.php?userid=' . $_GET['userid']);
            die();
        }
    }

    return <<<HTML
<script>
$(document).ready(function(){
	$('[href*="affiliates.php?action=edit&id="], [href*="clientssummary.php?userid="][href*="&activateaffiliate=true&token="]').closest('li').after(('<li><a href="#" id="kata_BulkAutoRecalculate"><i class="fas fa-fw fa-sliders-h" style="width:16px;text-align:center;"></i> Bulk Auto Recalculate</a></li>'));
		$('#kata_BulkAutoRecalculate').on('click', function(e){
			e.preventDefault();
			$('#modalAjaxTitle').html('Bulk Auto Recalculate');
			$('#modalAjaxBody').html('<div class="container col-md-12"><div class="row"><div class="col-md-6 text-center"><div class="panel panel-default"><div class="panel-body"><p><i class="fas fa-box fa-5x"></i></p><p><small>Auto Recalculate Customer\'s <strong>Products/Services</strong></small></p><p><a href="clientssummary.php?userid={$_GET['userid']}&kata=bulkAutoRecalculateP" class="btn btn-info btn-block">Recalculate Now »</a></p></div></div></div><div class="col-md-6 text-center"><div class="panel panel-default"><div class="panel-body"><p><i class="fas fa-globe fa-5x"></i></p><p><small>Auto Recalculate Customer\'s <strong>Domains</strong></small></p><p><a href="clientssummary.php?userid={$_GET['userid']}&kata=bulkAutoRecalculateD" class="btn btn-info btn-block">Recalculate Now »</a></p></div></div></div></div></div>');
			$('#modalAjax .modal-submit').addClass('hidden');
			$('#modalAjaxLoader').hide();
			$('#modalAjax .modal-dialog').addClass('modal-lg');
			$('#modalAjax').modal('show');
		})
	})
</script>
HTML;
});
