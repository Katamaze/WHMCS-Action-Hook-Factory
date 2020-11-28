<?php

/**
 * Restrict Domain Billing Cycles based on TLD
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 * @license     MIT License
 * @tested      WHMCS 7.10.2
 */

use WHMCS\Database\Capsule;

add_hook('ClientAreaPage', 1, function($vars)
{
    $restrictedTLDs = array('.com'); // Specify TLD for which you want to restrict billing cycles. TLD must start with a dot "."
    $restrictedPeriods = array('2', '3', '4', '5', '6', '7', '8', '9', '10'); // Any value from 1 to 10 (1 year, 2 years, 3 years... 10 years)

    if (!$restrictedTLDs OR !$restrictedPeriods): return; endif;

    foreach ($vars['domains'] as $k => $v)
    {
        $tld = Capsule::select(Capsule::raw('SELECT extension FROM tbldomainpricing WHERE "' . $v['domain'] . '" LIKE CONCAT("%", extension) ORDER BY LENGTH(extension) DESC LIMIT 1'))[0]->extension;

        if (in_array($tld, $restrictedTLDs))
        {
            foreach ($restrictedPeriods as $i)
            {
                unset($vars['domains'][$k]['pricing'][$i]);
            }
        }
    }

    return array('domains' => $vars['domains']);
});
