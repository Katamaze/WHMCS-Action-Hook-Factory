<?php

/**
 * Rename Addon Module Label
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

add_hook('AdminAreaHeaderOutput', 1, function($vars)
{
    return <<<HTML
    <script type="text/javascript">
    $(document).ready(function(){
        $("a[id='Menu-Addons-Mercury']").text('CMS');
        $("a[id='Menu-Addons-Commission Manager']").text('Affiliates');
        $("a[id='Menu-Addons-Billing Extension']").text('Accounting');
    });
    </script>
HTML;
});
