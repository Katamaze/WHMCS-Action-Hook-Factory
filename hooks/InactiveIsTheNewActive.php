<?php

/**
 * Inactive is the new Active
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 * USE AT YOUR OWN RISK
 * 
 * In the middle of the COVID-19 crisis WHMCS increased prices up to 3154%
 * Some companies will start paying 15.599 $ per year instead of 479 $
 *
 * I stopped supporting WHMCS a long time ago due to the following reasons:
 * https://katamaze.com/blog/41/whmcs-cons
 *
 * I'm no longer in the mood to help this company with my money, efforts and skills
 * I stopped wasting my time with their untested releses full of bugs and features no one asked for
 * I also stopped adding new features to my modules and I no longer partecipate to whmcs.community
 *
 * They don't deserve it
 * 
 * That being said, the following script can surely help to figure out what to do
 * I prefer not to describe what it does but it's not rocket science
 * This is something I coded in less than 10 minutes
 *
 * I can think of 2 more ways to achieve the same goal in more stylish ways (all legitimate)
 * Anyway I moved to another market leaving WHMCS so I have no time to dedicate to this project
 */

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
