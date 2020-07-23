<?php

/**
 * Promotion Code in Invoice Payment Confirmation
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    if (in_array($vars['messagename'], array('Invoice Payment Confirmation')))
    {
        $promotions = Capsule::select(Capsule::raw('SELECT description FROM tblinvoiceitems WHERE invoiceid = "' . $vars['relid'] . '" AND type = "PromoHosting"'));

        foreach ($promotions as $v)
        {
            $merge_fields['assigned_promos'][] = $v->description;
        }

        return $merge_fields;
    }
});
