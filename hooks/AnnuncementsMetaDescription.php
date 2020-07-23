<?php

/**
 * Announcements Meta Description
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    if ($vars['templatefile'] == 'viewannouncement')
    {
    return <<<HTML
<meta name="description" content="{$vars['summary']}">
HTML;
    }
});
