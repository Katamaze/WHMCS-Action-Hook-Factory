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
                $temp[$v->server][$v->id] = array('id' => $v->id, 'userid' => $v->userid, 'username' => $v->username, 'external-id' => $externalID[$v->userid]);
            }
        }

        if ($temp)
        {
            require_once('PleskApiClient.php');

            foreach ($temp as $serverID => $packages)
            {
                $plesk = new PleskApiClient($output['servers'][$serverID]['hostname']);
                $plesk->setCredentials($output['servers'][$serverID]['username'], $output['servers'][$serverID]['password']);

                $request .= <<<EOF
<packet version="1.6.3.0">
<customer>
EOF;

                foreach ($packages as $pid => $package)
                {
                    $login = ($package['external-id'] ? $package['external-id'] : $package['username']);

                    $request .= <<<EOF
    <get>
        <filter>
            <login>{$login}</login>
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
                $i = 0;

                foreach ($response->customer->get as $v)
                {
                    if ($v->result->errcode == '1013')
                    {
                        $key = array_search($v->result->{'filter-id'}->__toString(), $externalID);
                        $hostingList = Capsule::table('tblhosting')->where('userid', $key)->where('server', $serverID)->pluck('domain', 'id');
                        $output['error']['externalID'][$serverID][$key] = array('userid' => $key, 'external_id' => $v->result->{'filter-id'}->__toString(), 'server' => $serverID, 'accounts' => $hostingList);
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
