<?php

/**
 * Related Service in Info Ticket Sidebar
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;
use WHMCS\View\Menu\Item as MenuItem;

add_hook('ClientAreaPrimarySidebar', 1, function (MenuItem $primarySidebar)
{
    if (!is_null($primarySidebar->getChild('Ticket Information')))
    {
        $relatedService = Capsule::table('tbltickets')->where('tid', '=', $_GET['tid'])->pluck('service')[0];
        if (!$relatedService): return; endif;

        $serviceType = substr($relatedService, 0, 1);
        $relatedService = substr($relatedService, 1);

        if ($serviceType == 'D')
        {
            $url = 'clientarea.php?action=domaindetails&id=' . $relatedService;
            $target = Capsule::table('tbldomains')->where('id', '=', $relatedService)->pluck('domain')[0];
            $icon = '<i class="fas fa-globe fa-fw" style="float:none;"></i>';
            $label = $icon . ' <a href="' . $url . '">' . $target . '</a>';
        }
        elseif ($serviceType == 'S')
        {
            $url = 'clientarea.php?action=productdetails&id=' . $relatedService;
            $target = Capsule::table('tblhosting')->leftJoin('tblproducts', 'tblhosting.packageid', '=', 'tblproducts.id')->where('tblhosting.id', '=', $relatedService)->pluck('tblproducts.name')[0];
            $icon = '<i class="fas fa-server fa-fw" style="float:none;"></i>';
            $label = $icon . ' <a href="' . $url . '">' . $target . '</a>';
        }

        $primarySidebar->getChild('Ticket Information')
        ->addChild('Related Service')
        ->setClass('ticket-details-children')
        ->setLabel('<span class="title">Related Service</span><br>' . $label)
        ->setOrder(20);
    }
});
