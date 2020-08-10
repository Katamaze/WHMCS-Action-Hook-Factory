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
        $("a[id='Menu-Addons-CURRENT NAME']").text('NEW NAME');
    });
    </script>
HTML;
});
