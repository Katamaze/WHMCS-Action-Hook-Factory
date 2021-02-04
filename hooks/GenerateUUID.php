<?php

/**
 * Generate WHMCS UUID for clients
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;
use Ramsey\Uuid\Uuid;

add_hook('ClientAreaPage', 1, function($vars)
{
    $clients = Capsule::table('tblclients')->where('uuid', '')->pluck('id');

    foreach ($clients as $v)
    {
        Capsule::table('tblclients')->where('id', $v)->update(['uuid' => Uuid::uuid4()]);
    }
});
