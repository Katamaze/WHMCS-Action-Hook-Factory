<?php

/**
 * Plesk Checker
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 */

use WHMCS\Database\Capsule;

class Checker
{
    function Plesk()
    {
        foreach (Capsule::table('tblservers')->where('type', 'plesk')->where('disabled', '0')->where('hostname', '!=', '')->where('username', '!=', '')->where('password', '!=', '')->get(['id', 'ipaddress', 'hostname', 'username', 'password']) as $v)
        {
            $v->hostname = ($v->hostname ? $v->hostname : $v->ipaddress);
            unset($v->ipaddress);
            $v->password = Decrypt($v->password);
            $output['servers'][$v->id] = (array) $v;
        }

        if (!$output['servers']): return array('error' => 'No Plesk servers Found. Please, check your <a href="configservers.php">Servers.</a>'); endif;
        $servers = array_column($output['servers'], 'id');

        $externalID = Capsule::table('mod_pleskaccounts')->pluck('panelexternalid', 'userid');

        foreach (Capsule::table('tblhosting')->whereIn('domainstatus', ['Active', 'Suspended', 'Completed'])->whereIn('server', $servers)->get(['id', 'username', 'domain', 'server', 'userid']) as $v)
        {
            if (!$v->domain)
            {
                $output['error']['noDomain'][$v->id] = array('id' => $v->id, 'userid' => $v->userid, 'server' => $v->server);
            }
            elseif (!$v->username)
            {
                $output['error']['noUsername'][$v->id] = array('id' => $v->id, 'userid' => $v->userid, 'domain' => $v->domain, 'server' => $v->server);
            }
            elseif (count(explode(' ', $v->username)) > 1)
            {
                $output['error']['usernameSpace'][$v->id] = array('id' => $v->id, 'userid' => $v->userid, 'domain' => $v->domain, 'username' => $v->username, 'server' => $v->server);
            }
            else
            {
                $output['servers'][$v->server]['accounts'] = $i++;
                $temp[$v->server][] = array('id' => $v->id, 'userid' => $v->userid, 'username' => $v->username, 'external-id' => $externalID[$v->userid], 'domain' => $v->domain, 'server' => $v->server);
            }
        }

        $i = 0;

        if ($temp)
        {
            require_once('PleskAPIClient.php');

            foreach ($temp as $serverID => $packages)
            {
                $plesk = new PleskApiClient($output['servers'][$serverID]['hostname']);
                $plesk->setCredentials($output['servers'][$serverID]['username'], $output['servers'][$serverID]['password']);

                $request .= <<<EOF
<packet version="1.6.3.0">
<customer>
EOF;

                foreach ($packages as $package)
                {
                    $request .= <<<EOF
    <get>
        <filter>
            <login>{$package['username']}</login>
        </filter>
        <dataset>
            <gen_info/>
        </dataset>
    </get>
EOF;
                }

                $request .= <<<EOF
</customer>
</packet>
EOF;
                $response = $plesk->request($request);
                $response = new SimpleXMLElement($response);
                $response = json_decode(json_encode($response), true);

                foreach ($response['customer']['get'] as $k => $v)
                {
                    if ($v['result']['errtext'] == 'client does not exist')
                    {
                        $output['error']['clientNotFound'][$temp[$serverID][$k]['userid']] = array('id' => $temp[$serverID][$k]['id'], 'userid' => $temp[$serverID][$k]['userid'], 'domain' => $temp[$serverID][$k]['domain'], 'username' => $temp[$serverID][$k]['username'], 'server' => $temp[$serverID][$k]['server']);
                    }
                    elseif (!$v['result']['data']['gen_info']['external-id'] AND $temp[$serverID][$k]['external-id'])
                    {
                        $hostingList = Capsule::table('tblhosting')->where('userid', $temp[$serverID][$k]['userid'])->where('server', $serverID)->pluck('domain', 'id');
                        $output['error']['externalID'][$serverID][$temp[$serverID][$k]['userid']] = array('userid' => $temp[$serverID][$k]['userid'], 'username' => $v['result']['filter-id'], 'external_id' => $temp[$serverID][$k]['external-id'], 'server' => $serverID, 'accounts' => $hostingList);
                        $i++;
                    }
                }

                unset($plesk, $request, $response);
            }

            $output['externalIDCount'] = $i;
        }

        return $output;
    }
}
