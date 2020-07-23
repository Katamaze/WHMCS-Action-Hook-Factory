<?php

/**
 * Add button next to module's functions
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
        $("#modcmdbtns").append('<button type="button" class="btn btn-danger"><i class="fa fa-shower" aria-hidden="true"></i> Bath Time</button>');
    });
    </script>
HTML;
});
