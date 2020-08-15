<?php

/**
 * Knowledgebase Author
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AdminAreaHeadOutput', 1, function($vars)
{
    if ($vars['filename'] == 'supportkb' AND $_GET['action'] == 'edit' AND $_GET['id'])
    {
        if (!Capsule::schema()->hasColumn('tblknowledgebase', 'kt_author'))
        {
            Capsule::select(Capsule::raw('ALTER TABLE `tblknowledgebase` ADD `kt_author` INT NULL AFTER `language`'));
        }

        $adminID = Capsule::table('tblknowledgebase')->where('id', $_GET['id'])->pluck('kt_author')[0];

        if (!$adminID)
        {
            $adminID = $_SESSION['adminid'];
            Capsule::table('tblknowledgebase')->where('id', $_GET['id'])->update(['kt_author' => $adminID]);
        }

        $adminUsername = Capsule::table('tbladmins')->where('id', $adminID)->pluck('username')[0];

        return <<<HTML
<script>
$(document).ready(function(){
$('table[class="form"] > tbody tr:first').after(('<tr><td class="fieldlabel" width="15%">Author</td><td class="fieldarea"><a href="configadmins.php?action=manage&id={$adminID}"><i class="fas fa-user"></i> {$adminUsername}</a></td></tr>'));
})
</script>
HTML;
    }
});
