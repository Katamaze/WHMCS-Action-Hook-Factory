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
    if (($vars['filename'] == 'cart' AND $_GET['a'] == 'view') OR $vars['templatefile'] == 'domain-renewals')
    {
        $restrictedTLDs = array('.com'); // Specify TLD for which you want to restrict billing cycles. TLD must start with a dot "."
        $restrictedPeriods = array('2', '3', '4', '5', '6', '7', '8', '9', '10'); // Any value from 1 to 10 (1 year, 2 years, 3 years... 10 years)

        if (!$restrictedTLDs OR !$restrictedPeriods): return; endif;
        $key = ($vars['templatefile'] == 'domain-renewals' ? 'renewalsData' : 'domains');

        foreach ($vars[$key] as $k => $v)
        {
            $tld = Capsule::select(Capsule::raw('SELECT extension FROM tbldomainpricing WHERE "' . $v['domain'] . '" LIKE CONCAT("%", extension) ORDER BY LENGTH(extension) DESC LIMIT 1'))[0]->extension;

            if (in_array($tld, $restrictedTLDs))
            {
                if ($key == 'domains')
                {
                    foreach ($restrictedPeriods as $i)
                    {
                        unset($vars[$key][$k]['pricing'][$i]);
                    }
                }
                else
                {
                    foreach ($vars[$key][$k]['renewalOptions'] as $k2 => $v2)
                    {
                        if (in_array($vars[$key][$k]['renewalOptions'][$k2]['period'], $restrictedPeriods))
                        {
                            unset($vars[$key][$k]['renewalOptions'][$k2]);
                        }
                    }
                }
            }
        }

        return array($key => $vars[$key]);
    }
});
