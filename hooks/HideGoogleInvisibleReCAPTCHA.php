<?php

/**
 * Hide Google Invisible reCAPTCHA Badge
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    return <<<HTML
<style>
.grecaptcha-badge { opacity:0 }
</style>
HTML;
});
