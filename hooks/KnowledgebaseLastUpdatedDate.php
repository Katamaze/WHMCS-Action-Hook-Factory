<?php

/**
 * Knowledgebase Last Updated Date
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('ClientAreaPageKnowledgebase', 1, function($vars)
{
	if ($vars['kbarticle']['id'])
	{
		$LastUpdated = Capsule::table('tblactivitylog')->where('description', 'like', 'Modified Knowledgebase Article ID: %' . $vars['kbarticle']['id'])->orderby('id', 'desc')->first(['date'])->date;
		$output['kbarticle'] = $vars['kbarticle'];
		$output['kbarticle']['lastupdated']['date'] = date('Y-m-d', strtotime($LastUpdated));
		$output['kbarticle']['lastupdated']['time'] = date('H:i:s', strtotime($LastUpdated));

		return $output;
	}
});
