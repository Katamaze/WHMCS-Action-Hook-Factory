<?php

/**
 * Assign Client to Group based on purchased Product/Service v2
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    // Define group/product pairs. Instructions provided below
    // https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/README.md#client-to-group-based-on-purchased-productservice
    $groups['products']['1'] = array('1', '2', '3');
    $groups['products']['2'] = array('4');
    $groups['productaddons']['1'] = array('2');
    $groups['configurableoption']['3'] = array('5' => true, '6' => array('7', '8', '10'));

    if (!$groups): return; endif;

    if (in_array($vars['messagename'], array('CodeGuard Welcome Email', 'Dedicated/VPS Server Welcome Email', 'Hosting Account Welcome Email', 'Marketgoo Welcome Email', 'Other Product/Service Welcome Email', 'Reseller Account Welcome Email', 'SHOUTcast Welcome Email', 'SiteLock VPN Welcome Email', 'SiteLock Welcome Email', 'SpamExperts Welcome Email', 'Weebly Welcome Email')))
    {
        $orderedProducts = Capsule::table('tblhosting')->where('orderid', $vars['mergefields']['service_order_id'])->pluck('packageid');
        $orderedProductAddons = Capsule::table('tblhostingaddons')->where('orderid', $vars['mergefields']['service_order_id'])->pluck('addonid');

        if ($groups['configurableoption'])
        {
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
        }

        foreach ($groups['products'] as $group => $target)
        {
            if (array_intersect($orderedProducts, $target))
            {
                Capsule::table('tblclients')->where('id', $vars['mergefields']['client_id'])->where('groupid', '0')->update(['groupid' => $group]);
                $groupName = Capsule::table('tblclientgroups')->where('id', $group)->pluck('groupname')[0];
                $merge_fields['client_group_id'] = $group;
                $merge_fields['client_group_name'] = $groupName;
                $return = true;
                break;
            }
        }

        if ($return): return $merge_fields; endif;

        foreach ($groups['productaddons'] as $group => $target)
        {
            if (array_intersect($orderedProductAddons, $target))
            {
                Capsule::table('tblclients')->where('id', $vars['mergefields']['client_id'])->where('groupid', '0')->update(['groupid' => $group]);
                $groupName = Capsule::table('tblclientgroups')->where('id', $group)->pluck('groupname')[0];
                $merge_fields['client_group_id'] = $group;
                $merge_fields['client_group_name'] = $groupName;
                $return = true;
                break;
            }
        }

        if ($return): return $merge_fields; endif;

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
                            Capsule::table('tblclients')->where('id', $vars['mergefields']['client_id'])->where('groupid', '0')->update(['groupid' => $group]);
                            $groupName = Capsule::table('tblclientgroups')->where('id', $group)->pluck('groupname')[0];
                            $merge_fields['client_group_id'] = $group;
                            $merge_fields['client_group_name'] = $groupName;
                            $return = true;
                            break;
                        }
                    }
                }
                else
                {
                    foreach ($orderedConfigurableOptions as $target)
                    {
                        if (in_array($configID, $target))
                        {
                            Capsule::table('tblclients')->where('id', $vars['mergefields']['client_id'])->where('groupid', '0')->update(['groupid' => $group]);
                            $groupName = Capsule::table('tblclientgroups')->where('id', $group)->pluck('groupname')[0];
                            $merge_fields['client_group_id'] = $group;
                            $merge_fields['client_group_name'] = $groupName;
                            $return = true;
                            break;
                        }
                    }
                }
            }
        }

        if ($return): return $merge_fields; endif;
    }
});
