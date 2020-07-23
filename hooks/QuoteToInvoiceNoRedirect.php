<?php

/**
 * Disable redirect when converting Quote to Invoice
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use Illuminate\Database\Capsule\Manager as Capsule;

add_hook('InvoiceCreation', 1, function($vars)
{
    $quoteID = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->where('notes', 'like', 'Re Quote #%')->first(['notes']);
    header('Location: quotes.php?action=manage&id=' . explode('#', $quoteID->notes)[1]);
    die();
});
