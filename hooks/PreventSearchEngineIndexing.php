<?php

/**
 * Prevent indexing on search engines
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    return <<<HTML
<meta name="robots" content="noindex">
HTML;
});
