<?php

/**
 * Chatstack Disable for Logged-In Users and Administrators
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

define('kt_disableAdminTracking', true);
define('kt_disableForLoggedIn', true);

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    if (kt_disableAdminTracking AND !$vars['adminMasqueradingAsClient'] AND !$vars['adminLoggedIn'])
    {
        $host = ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : parse_url($vars['systemurl'])['host']);

        return <<<HTML
<!-- START chatstack.com Live Chat HTML Code -->
<script type="text/javascript">
<!--
  var Chatstack = { server: '{$host}/modules' };
  (function(d, undefined) {
    // JavaScript
    Chatstack.e = []; Chatstack.ready = function (c) { Chatstack.e.push(c); }
    var b = d.createElement('script'); b.type = 'text/javascript'; b.async = true;
    b.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + Chatstack.server + '/livehelp/scripts/js.min.js';
    var s = d.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(b, s);
  })(document);
-->
</script>
<!-- END chatstack.com Live Chat HTML Code -->
HTML;
    }
});

add_hook('ClientAreaFooterOutput', 1, function($vars)
{
    if (kt_disableForLoggedIn AND $vars['loggedin'])
    {
        return <<<HTML
<script>
Chatstack.ready(function () {
    $("#chatstack-launcher-frame").remove();
});
</script>
HTML;
    }
});
