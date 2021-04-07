<?php

/**
 * Inactive is the new Active? Just testing stuff
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AdminAreaHeadOutput', 1, function($vars) {

    if ($vars['filename'] == 'clients' OR in_array($_GET['rp'], array('/admin/services', '/admin/addons', '/admin/domains'))) {

        $autoPost = <<<HTML
if ($('input#checkboxShowHidden').is(':checked')) {

    $('body').addClass('hidden');
    $('#checkboxShowHidden').click();
}
HTML;

    }

    return <<<HTML
<script>
$(document).on('ready', function() {

    if ($('input#intelliSearchHideInactiveSwitch').is(':checked')) {

        $('#intelliSearchHideInactiveSwitch').click();
    }

    {$autoPost}
})
</script>
HTML;
});
