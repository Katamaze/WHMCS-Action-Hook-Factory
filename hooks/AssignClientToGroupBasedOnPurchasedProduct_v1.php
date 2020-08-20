<?php

/**
 * Assign Client to Group based on purchased Product/Service v1
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('AcceptOrder', 1, function($vars)
{
    // Define group/product pairs. Instructions provided below
    // https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/README.md#client-to-group-based-on-purchased-productservice
    $groups['products']['1'] = array('1', '2', '3');
    $groups['products']['2'] = array('4');
    $groups['productaddons']['1'] = array('2');
    $groups['configurableoption']['1'] = array('5' => array('7', '8', '10'), '6' => true);

    $userID = Capsule::table('tblorders')->where('id', $vars['orderid'])->pluck('userid')[0];
    $orderedProducts = Capsule::table('tblhosting')->where('orderid', $vars['orderid'])->pluck('packageid', 'id');
    $orderedProductAddons = Capsule::table('tblhostingaddons')->where('orderid', $vars['orderid'])->pluck('addonid');

    foreach (Capsule::select(Capsule::raw('SELECT t1.relid, t1.configid, t1.optionid, t1.qty, t2.optiontype FROM tblhostingconfigoptions as t1 LEFT JOIN tblproductconfigoptions AS t2 ON t1.configid = t2.id LEFT JOIN tblproductconfigoptionssub AS t3 ON t1.optionid = t3.id WHERE t1.relid IN (\'' . implode('\',\'', array_keys($orderedProducts)) . '\')')) as $v)
    {
        $relid = $v->relid;
        $configid = $v->configid;

        if (in_array($v->optiontype, array('3', '4')))
        {
            $value = ($v->qty ? true : false);
        }
        else
        {
            $value = $v->optionid;
        }

        unset($v);

        if ($value)
        {
            $orderedConfigurableOptions[$relid][$configid] = $value;
        }
    }

    foreach ($groups['products'] as $group => $target)
    {
        if (array_intersect($orderedProducts, $target))
        {
            Capsule::table('tblclients')->where('id', $userID)->where('groupid', '0')->update(['groupid' => $group]);
            return;
        }
    }

    foreach ($groups['productaddons'] as $group => $target)
    {
        if (array_intersect($orderedProductAddons, $target))
        {
            Capsule::table('tblclients')->where('id', $userID)->where('groupid', '0')->update(['groupid' => $group]);
            return;
        }
    }

    foreach ($groups['configurableoption'] as $group => $configurableOptions)
    {
        foreach ($configurableOptions as $configID => $options)
        {
            if (is_array($options))
            {
                foreach ($orderedConfigurableOptions as $target)
                {
                    if (array_intersect($options, $target))
                    {
                        Capsule::table('tblclients')->where('id', $userID)->where('groupid', '0')->update(['groupid' => $group]);
                        return;
                    }
                }
            }
            else
            {

                foreach ($orderedConfigurableOptions as $target)
                {
                    if (in_array($configID, $target))
                    {
                        Capsule::table('tblclients')->where('id', $userID)->where('groupid', '0')->update(['groupid' => $group]);
                        return;
                    }
                }
            }
        }
    }
});
