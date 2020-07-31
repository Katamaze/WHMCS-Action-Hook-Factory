<?php

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

$dateFilter = Carbon::create($year, $month, 1);
$startOfMonth = $dateFilter->startOfMonth()->toDateTimeString();
$endOfMonth = $dateFilter->endOfMonth()->toDateTimeString();

$reportdata["title"] = 'Churn Rate for ' . $year;
$reportdata["description"] = "Rate at which customers stop doing business with you.";
$reportdata["yearspagination"] = true;
$reportdata["tableheadings"] = array(
    'Date',
    'Products',
    '<strong class="text-success"><i class="far fa-plus-square"></i></strong>',
    '<strong class="text-danger"><i class="far fa-minus-square"></i></strong>',
    '<strong><i class="fas fa-percentage"></i></strong>',
    'Domains',
    '<strong class="text-success"><i class="far fa-plus-square"></i></strong>',
    '<strong class="text-danger"><i class="far fa-minus-square"></i></strong>',
    '<strong><i class="fas fa-percentage"></i></strong>',
    'Overall',
    '<strong class="text-success"><i class="far fa-plus-square"></i></strong>',
    '<strong class="text-danger"><i class="far fa-minus-square"></i></strong>',
    '<strong><i class="fas fa-percentage"></i></strong>',
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

for ($tmonth = 1; $tmonth <= 12; $tmonth++)
{
    if (date('Y') == $year AND $tmonth > str_replace('0', '', $month)): continue; endif;

    $date = Carbon::create($year, $tmonth, 1);
    $dateMonthYear = $date->format('M Y');
    $dateMonth = $date->format('M');
    $key = $year . '-' . $tmonth;

    // Products
    $activeProducts = $activeProducts + $reportvalues['productsNew'][$key];
    $productStart[$tmonth] = $activeProducts;
    $reportvalues['productsCumulative'][$key] = $activeProducts;
    $productsNew = isset($reportvalues['productsNew'][$key]) ? $reportvalues['productsNew'][$key] : '0';
    $productsTerminated = isset($reportvalues['productsTerminated'][$key]) ? $reportvalues['productsTerminated'][$key] : '0';
    $productsCumulative = isset($reportvalues['productsCumulative'][$key]) ? $reportvalues['productsCumulative'][$key] : '0';
    $productVariation = $productsNew - $productsTerminated;
    $productChurnRate = number_format(($productsTerminated / $productStart[($tmonth == '1' ? '1' : $tmonth - 1)]) * 100, 1, '.', '') + 0;

    // Domains
    $activeDomains = $activeDomains + $reportvalues['domainsNew'][$key];
    $domainStart[$tmonth] = $activeDomains;
    $reportvalues['domainsCumulative'][$key] = $activeDomains;
    $domainsNew = isset($reportvalues['domainsNew'][$key]) ? $reportvalues['domainsNew'][$key] : '0';
    $domainsTerminated = isset($reportvalues['domainsTerminated'][$key]) ? $reportvalues['domainsTerminated'][$key] : '0';
    $domainsCumulative = isset($reportvalues['domainsCumulative'][$key]) ? $reportvalues['domainsCumulative'][$key] : '0';
    $domainVariation = $domainsNew - $domainsTerminated;
    $domainChurnRate = number_format(($domainsTerminated / $domainStart[($tmonth == '1' ? '1' : $tmonth - 1)]) * 100, 1, '.', '') + 0;

    // Overall
    $activeOverall = $activeProducts + $activeDomains;
    $overallStart[$tmonth] = $activeOverall;
    $reportvalues['overallCumulative'][$key] = $activeOverall;
    $overallNew = $productsNew + $domainsNew;
    $overallTerminated = $productsTerminated + $domainsTerminated;
    $overallCumulative = $productsCumulative + $domainsCumulative;
    $overallVariation = $productVariation + $domainVariation;
    $overallChurnRate = $productChurnRate + $domainChurnRate;

    $reportdata['tablevalues'][] = array(
        $dateMonthYear,
        formatCell(array('col' => 'products=', 'variation' => $productVariation, 'start' => $productStart[($tmonth == '1' ? '1' : $tmonth - 1)], 'end' => $productStart[($tmonth == '1' ? '1' : $tmonth - 1)] + $productVariation)),
        formatCell(array('col' => 'products+', 'increase' => $productsNew)),
        formatCell(array('col' => 'products-', 'decrease' => $productsTerminated)),
        formatCell(array('col' => 'products%', 'churnRate' => $productChurnRate)),
        formatCell(array('col' => 'domains=', 'variation' => $domainVariation, 'start' => $domainStart[($tmonth == '1' ? '1' : $tmonth - 1)], 'end' => $domainStart[($tmonth == '1' ? '1' : $tmonth - 1)] + $domainVariation)),
        formatCell(array('col' => 'domains+', 'increase' => $domainsNew)),
        formatCell(array('col' => 'domains-', 'decrease' => $domainsTerminated)),
        formatCell(array('col' => 'domains%', 'churnRate' => $domainChurnRate)),
        formatCell(array('col' => 'overall=', 'variation' => $overallVariation, 'start' => $overallStart[($tmonth == '1' ? '1' : $tmonth - 1)], 'end' => $overallStart[($tmonth == '1' ? '1' : $tmonth - 1)] + $overallVariation)),
        formatCell(array('col' => 'overall+', 'increase' => $overallNew)),
        formatCell(array('col' => 'overall-', 'decrease' => $overallTerminated)),
        formatCell(array('col' => 'overall%', 'churnRate' => $overallChurnRate)),
    );

    $chartdata['rows'][] = array(
        'c'=>array(
            array('v' => $dateMonth),
            array('v' => (int)$productStart[($tmonth == '1' ? '1' : $tmonth - 1)] + $productVariation),
            array('v' => (int)$domainStart[($tmonth == '1' ? '1' : $tmonth - 1)] + $domainVariation),
            array('v' => (int)$overallStart[($tmonth == '1' ? '1' : $tmonth - 1)] + $overallVariation),
        )
    );
}

function formatCell($data)
{
    /**
     * @param       string      $col            Column type
     * @param       string      $variation      Monthly change
     * @param       string      $increase       New purchases
     * @param       string      $decrease       New terminations
     * @param       string      $start          No. of customers (at the start of the period)
     * @param       string      $end            No. of customers (at the end of the period)
     * @param       string      $churnRate      Churn Rate
     * @return      string                      Formatted HTML cell
     */
    $data['variation'] = ($data['variation'] ? $data['variation'] : '0');
    $data['increase'] = ($data['increase'] ? $data['increase'] : '0');
    $data['decrease'] = ($data['decrease'] ? $data['decrease'] : '0');
    $data['start'] = ($data['start'] ? $data['start'] : '0');
    $data['end'] = ($data['end'] ? $data['end'] : '0');
    $data['churnRate'] = ($data['churnRate'] ? $data['churnRate'] : false);

    if (in_array($data['col'], array('products+', 'domains+', 'overall+')))
    {
        if ($data['increase'])
        {
            return '<span>' . $data['increase'] . '</span>';
        }
        else
        {
            return '-';
        }
    }
    elseif (in_array($data['col'], array('products-', 'domains-', 'overall-')))
    {
        if ($data['decrease'])
        {
            return '<span>' . $data['decrease'] . '</span>';
        }
        else
        {
            return '-';
        }
    }
    elseif (in_array($data['col'], array('products=', 'domains=', 'overall=')))
    {
        if ($data['variation'] > '0')
        {
            $variation = '<small class="pull-right" style="opacity:0.8;">' . abs($data['variation']) . '<i class="fad fa-angle-double-up fa-fw text-success"></i></span>';
        }
        elseif ($data['variation'] < '0')
        {
            $variation = '<small class="pull-right" style="opacity:0.8;">' . abs($data['variation']) . '<i class="fad fa-angle-double-down fa-fw text-danger"></i></span>';
        }

        if ($data['start'] != $data['end'])
        {
            return $data['start'] . ' <i class="fas fa-angle-right fa-fw"></i> ' . $data['end'] . $variation;
        }
        else
        {
            return $data['start'];
        }
    }
    elseif (in_array($data['col'], array('products%', 'domains%', 'overall%')))
    {
        if ($data['churnRate'] > 0)
        {
            return '<span class="label label-danger">' . $data['churnRate'] . '%</span>';
        }
        else
        {
            return '-';
        }
    }
}

$chartdata['cols'][] = array('label'=>'Day','type'=>'string');
$chartdata['cols'][] = array('label'=>'Products','type'=>'number');
$chartdata['cols'][] = array('label'=>'Domains','type'=>'number');
$chartdata['cols'][] = array('label'=>'Overall','type'=>'number');

$args = array();
$args['legendpos'] = 'right';

$reportdata["headertext"] = $chart->drawChart('Area',$chartdata,$args,'400px');
