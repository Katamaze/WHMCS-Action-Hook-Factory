<?php

// WARNING! I'M STILL WORKING ON IT - IT WILL BE READY IN HOURS

/**
 * Churn Rate
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 */

use WHMCS\Carbon;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

$dateFilter = Carbon::create(
    $year,
    $month,
    1
);

/** @var Carbon $today */
$startOfMonth = $dateFilter->startOfMonth()->toDateTimeString();
$endOfMonth = $dateFilter->endOfMonth()->toDateTimeString();

$reportdata["title"] = "Daily Performance for " . $months[(int) $month] . " " . $year;
$reportdata["description"] = "This report shows a daily activity summary for a given month.";

$reportdata["yearspagination"] = true;

$reportdata["tableheadings"] = array(
    "Date",
    "Products",
    "Domains",
);

$reportvalues = array();

// Products/Services
$groupBy = Capsule::raw('date_format(`regdate`, "%Y-%c")');
$reportvalues['productsNew'] = Capsule::table('tblhosting')->whereYear('regdate', '=', $year)->where('domainstatus', 'Active')->groupBy($groupBy)->orderBy('regdate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`regdate`, "%Y-%c") as month'));
$groupBy = Capsule::raw('date_format(`nextduedate`, "%Y-%c")');
$reportvalues['productsTerminated'] = Capsule::table('tblhosting')->whereYear('nextduedate', '=', $year)->where('nextduedate', '<=', $dateFilter->format('Y-m-d'))->whereNotIn('billingcycle', ['One Time', 'Completed'])->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%Y-%c") as month'));
$activeProducts = Capsule::table('tblhosting')->where('domainstatus', 'Active')->pluck(Capsule::raw('count(id) as total'))[0];

// Domains
$groupBy = Capsule::raw('date_format(`registrationdate`, "%Y-%c")');
$reportvalues['domainsNew'] = Capsule::table('tbldomains')->where('status', 'Active')->whereYear('registrationdate', '=', $year)->groupBy($groupBy)->orderBy('registrationdate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`registrationdate`, "%Y-%c") as month'));
$groupBy = Capsule::raw('date_format(`nextduedate`, "%Y-%c")');
$reportvalues['domainsTerminated'] = Capsule::table('tbldomains')->whereYear('nextduedate', '=', $year)->where('nextduedate', '<=', $dateFilter->format('Y-m-d'))->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%Y-%c") as month'));
$activeDomains = Capsule::table('tbldomains')->where('status', 'Active')->pluck(Capsule::raw('count(id) as total'))[0];

for ($month = 1; $month <= 12; $month++)
{
    $date = Carbon::create($year, $month, 1);
    $dateMonthYear = $date->format('M Y');
    $dateMonth = $date->format('M');
    $key = $year . '-' . $month;

    $productsNew = ($productsNew ? $productsNew : '0');
    $productsTerminated = ($productsTerminated ? $productsTerminated : '0');
    // Cumulative
    $activeProducts = $activeProducts + $reportvalues['productsNew'][$key];
    $productStart[$month] = $activeProducts;
    $reportvalues['productsCumulative'][$key] = $activeProducts;
    $activeDomains = $activeDomains + $reportvalues['domainsNew'][$key];
    $domainStart[$month] = $activeDomains;
    $reportvalues['domainsCumulative'][$key] = $activeDomains;

    $productsNew = isset($reportvalues['productsNew'][$key]) ? $reportvalues['productsNew'][$key] : '0';
    $productsTerminated = isset($reportvalues['productsTerminated'][$key]) ? $reportvalues['productsTerminated'][$key] : '0';
    $productsCumulative = isset($reportvalues['productsCumulative'][$key]) ? $reportvalues['productsCumulative'][$key] : '0';
    $domainsNew = isset($reportvalues['domainsNew'][$key]) ? $reportvalues['domainsNew'][$key] : '0';
    $domainsTerminated = isset($reportvalues['domainsTerminated'][$key]) ? $reportvalues['domainsTerminated'][$key] : '0';
    $domainsCumulative = isset($reportvalues['domainsCumulative'][$key]) ? $reportvalues['domainsCumulative'][$key] : '0';

    $productVariation = $productsNew - $productsTerminated;
    $domainVariation = $domainsNew - $domainsTerminated;

    $reportdata["tablevalues"][] = array(
        $dateMonthYear,
        formatCell(array('variation' => $productVariation, 'increase' => $productsNew, 'decrease' => $productsTerminated, 'start' => $productStart[($month == '1' ? '1' : $month - 1)], 'end' => $productStart[($month == '1' ? '1' : $month - 1)] + $productVariation)),
        formatCell(array('variation' => $domainVariation, 'increase' => $domainsNew, 'decrease' => $domainsTerminated, 'start' => $domainStart[($month == '1' ? '1' : $month - 1)], 'end' => $domainsCumulative + $domainVariation)),
    );

    $chartdata['rows'][] = array(
        'c'=>array(
            array('v' => $dateMonth),
            array('v' => (int)$productsNew),
            array('v' => (int)$productsCumulative),
            array('v' => (int)$domainsNew),
            array('v' => (int)$domainsCumulative),
        )
    );

}

function formatCell($data)
{
    /**
     * @param       string      $variation      Monthly change
     * @param       string      $increase       New purchases
     * @param       string      $decrease       New terminations
     * @param       string      $start          No. of customers (at the start of the period)
     * @param       string      $end            No. of customers (at the end of the period)
     * @return      string                      Formatted HTML cell
     */
    $data['variation'] = ($data['variation'] ? $data['variation'] : '0');
    $data['increase'] = ($data['increase'] ? $data['increase'] : '0');
    $data['decrease'] = ($data['decrease'] ? $data['decrease'] : '0');
    $data['start'] = ($data['start'] ? $data['start'] : '0');
    $data['end'] = ($data['end'] ? $data['end'] : '0');
    $churnRate = number_format(($data['decrease'] / $data['start']) * 100, 1, '.', '') + 0;

    if ($churnRate)
    {
        $churnRate = ' <span class="label label-danger">' . $churnRate . '%</span>';
    }
    else
    {
        $churnRate = false;
    }

    if ($data['variation'] > '0')
    {
        $output .='<span class=""><i class="fad fa-angle-double-up fa-fw text-success"></i> ' . $data['variation'] . $churnRate;
    }
    elseif ($data['variation'] < '0')
    {
        $output .='<span class=""><i class="fad fa-angle-double-down fa-fw text-danger"></i> ' . $data['variation'] . $churnRate;
    }
    else
    {
        $output .='<span class=""><i class="fas fa-equals fa-fw text-primary"></i></span>';
    }

    $output .= '<small class="pull-right">[';
    $output .= '<span class="text-success">' . $data['increase'] . '++</span> <span class="text-danger">' . $data['decrease'] . '--</span> ' . $data['end'];
    $output .= ']</small>';

    return $output;
}

$chartdata['cols'][] = array('label'=>'Day','type'=>'string');
$chartdata['cols'][] = array('label'=>'Completed Orders','type'=>'number');
$chartdata['cols'][] = array('label'=>'New Invoices','type'=>'number');
$chartdata['cols'][] = array('label'=>'Paid Invoices','type'=>'number');
$chartdata['cols'][] = array('label'=>'Opened Tickets','type'=>'number');
$chartdata['cols'][] = array('label'=>'Ticket Replies','type'=>'number');
$chartdata['cols'][] = array('label'=>'Cancellation Requests','type'=>'number');

$args = array();
$args['legendpos'] = 'right';

$reportdata["headertext"] = $chart->drawChart('Area',$chartdata,$args,'400px');
