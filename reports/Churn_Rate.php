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

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

$dateFilter = Carbon::create($year, $month, 1);
$startOfMonth = $dateFilter->startOfMonth()->toDateTimeString();
$endOfMonth = $dateFilter->endOfMonth()->toDateTimeString();

$reportdata['title'] = 'Churn Rate for ' . $year;
$reportdata['description'] = 'Rate at which customers stop doing business with you. Visit <a href="https://github.com/Katamaze/WHMCS-Free-Action-Hooks#churn-rate" target="_blank">Github</a> or this <a href="https://katamaze.com/blog/32/whmcs-action-hooks-collection-2020-updated-monthly" target="_blank">post</a> if you need help interpreting data. Refer to this article for <a href="https://katamaze.com/docs/billing-extension/39/client-area#Customer-Retention" target="_blank">customer retention</a>.';
$reportdata['yearspagination'] = true;
$reportdata['tableheadings'] = array(
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
$mothMatrix = array('1' => '0', '2' => '0', '3' => '0', '4' => '0', '5' => '0', '6' => '0', '7' => '0', '8' => '0', '9' => '0', '10' => '0', '11' => '0', '12' => '0');

// Products/Services
$groupBy = Capsule::raw('date_format(`regdate`, "%c")');
$products['active']['previousYears'] = Capsule::table('tblhosting')->whereYear('regdate', '<=', $year - 1)->where('domainstatus', 'Active')->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->pluck(Capsule::raw('count(id) as total'))[0];
$products['active']['currentYear'] = Capsule::table('tblhosting')->whereYear('regdate', '=', $year)->where('domainstatus', 'Active')->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->groupBy($groupBy)->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`regdate`, "%c") as month'));
$products['active']['currentYear'] = $products['active']['currentYear'] + $mothMatrix;
ksort($products['active']['currentYear']);
$products['active']['total'] = $products['active']['previousYears'] + array_sum($products['active']['currentYear']);
$groupBy = Capsule::raw('date_format(`nextduedate`, "%c")');
$products['terminated'] = Capsule::table('tblhosting')->whereYear('nextduedate', '=', $year)->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%c") as month'));
$products['terminated'] = $products['terminated'] + $mothMatrix;
ksort($products['terminated']);
$products['variation'] = array_map('subtract', $products['active']['currentYear'], $products['terminated']);
$products['variation'] = array_combine(range(1, count($products['variation'])), array_values($products['variation']));

// Domains
$groupBy = Capsule::raw('date_format(`registrationdate`, "%c")');
$domains['active']['previousYears'] = Capsule::table('tbldomains')->whereYear('registrationdate', '<=', $year - 1)->where('status', 'Active')->pluck(Capsule::raw('count(id) as total'))[0];
$domains['active']['currentYear'] = Capsule::table('tbldomains')->whereYear('registrationdate', '=', $year)->where('status', 'Active')->groupBy($groupBy)->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`registrationdate`, "%c") as month'));
$domains['active']['currentYear'] = $domains['active']['currentYear'] + $mothMatrix;
ksort($domains['active']['currentYear']);
$domains['active']['total'] = $domains['active']['previousYears'] + array_sum($domains['active']['currentYear']);
$groupBy = Capsule::raw('date_format(`nextduedate`, "%c")');
$domains['terminated'] = Capsule::table('tbldomains')->whereYear('nextduedate', '=', $year)->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%c") as month'));
$domains['terminated'] = $domains['terminated'] + $mothMatrix;
ksort($domains['terminated']);
$domains['variation'] = array_map('subtract', $domains['active']['currentYear'], $domains['terminated']);
$domains['variation'] = array_combine(range(1, count($domains['variation'])), array_values($domains['variation']));

// Domains
$groupBy = Capsule::raw('date_format(`registrationdate`, "%c")');
$reportvalues['domainsNew'] = Capsule::table('tbldomains')->where('status', 'Active')->whereYear('registrationdate', '=', $year)->groupBy($groupBy)->orderBy('registrationdate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`registrationdate`, "%c") as month'));
$groupBy = Capsule::raw('date_format(`nextduedate`, "%c")');
$reportvalues['domainsTerminated'] = Capsule::table('tbldomains')->whereYear('nextduedate', '=', $year)->where('nextduedate', '<=', $dateFilter->format('Y-m-d'))->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%c") as month'));
$activeDomains = Capsule::table('tbldomains')->where('status', 'Active')->pluck(Capsule::raw('count(id) as total'))[0];

for ($tmonth = 1; $tmonth <= 12; $tmonth++)
{
    if (date('Y') == $year AND $tmonth > str_replace('0', '', $month)): continue; endif;

    $date = Carbon::create($year, $tmonth, 1);
    $dateMonthYear = $date->format('M Y');
    $dateMonth = $date->format('M');

    // Products
    if ($tmonth == '1')
    {
        $products['start'][$tmonth] = $products['active']['previousYears'] + $products['start'][$tmonth];
        $products['end'][$tmonth] = $products['start'][$tmonth] + $products['variation'][$tmonth];
    }
    else
    {
        $products['start'][$tmonth] = $products['end'][$tmonth - 1];
        $products['end'][$tmonth] = $products['start'][$tmonth] + $products['variation'][$tmonth];
    }

    // Domains
    if ($tmonth == '1')
    {
        $domains['start'][$tmonth] = $domains['active']['previousYears'] + $domains['start'][$tmonth];
        $domains['end'][$tmonth] = $domains['start'][$tmonth] + $domains['variation'][$tmonth];
    }
    else
    {
        $domains['start'][$tmonth] = $domains['end'][$tmonth - 1];
        $domains['end'][$tmonth] = $domains['start'][$tmonth] + $domains['variation'][$tmonth];
    }

    $productsChurnRate = number_format(($products['terminated'][$tmonth] / $products['start'][$tmonth]) * 100, 1, '.', '') + 0;
    $domainsChurnRate = number_format(($domains['terminated'][$tmonth] / $domains['start'][$tmonth]) * 100, 1, '.', '') + 0;

    if ($tmonth == str_replace('0', '', $month))
    {
        $dateMonthYear = '<span data-toggle="tooltip" data-placement="top" title="" data-original-title="Statistics for current month are inaccurate as renewals still have to occur">' . $dateMonthYear . ' <i class="fas fa-info-circle" style="opacity:0.8;"></i></span>';
    }

    $reportdata['tablevalues'][] = array(
        $dateMonthYear,
        formatCell(array('col' => 'products=', 'variation' => $products[$tmonth]['variation'], 'start' => $products['start'][$tmonth], 'end' => $products['end'][$tmonth])),
        formatCell(array('col' => 'products+', 'increase' => $products['active']['currentYear'][$tmonth])),
        formatCell(array('col' => 'products-', 'decrease' => $products['terminated'][$tmonth])),
        formatCell(array('col' => 'products%', 'churnRate' => $productsChurnRate)),
        formatCell(array('col' => 'domains=', 'variation' => $domains[$tmonth]['variation'], 'start' => $domains['start'][$tmonth], 'end' => $domains['end'][$tmonth])),
        formatCell(array('col' => 'domains+', 'increase' => $domains['active']['currentYear'][$tmonth])),
        formatCell(array('col' => 'domains-', 'decrease' => $domains['terminated'][$tmonth])),
        formatCell(array('col' => 'domains%', 'churnRate' => $domainsChurnRate)),
        formatCell(array('col' => 'overall=', 'variation' => $products[$tmonth]['variation'] + $domains[$tmonth]['variation'], 'start' => $products['start'][$tmonth] + $domains['start'][$tmonth], 'end' => $products['end'][$tmonth] + $domains['end'][$tmonth])),
        formatCell(array('col' => 'overall+', 'increase' => $products['active']['currentYear'][$tmonth] + $domains['active']['currentYear'][$tmonth])),
        formatCell(array('col' => 'overall-', 'decrease' => $products['terminated'][$tmonth] + $domains['terminated'][$tmonth])),
        formatCell(array('col' => 'overall%', 'churnRate' => $productsChurnRate + $domainsChurnRate))
    );

    $chartdata['rows'][] = array(
        'c'=>array(
            array('v' => $dateMonth),
            array('v' => (int)$products['end'][$tmonth]),
            array('v' => (int)$domains['end'][$tmonth]),
            array('v' => (int)$products['end'][$tmonth] + $domains['end'][$tmonth]),
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
     * @param       string      $start          No. of products/services (at the start of the period)
     * @param       string      $end            No. of products/services (at the end of the period)
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

function subtract($a, $b)
{
    return $a - $b;
}

$chartdata['cols'][] = array('label'=>'Day','type'=>'string');
$chartdata['cols'][] = array('label'=>'Products','type'=>'number');
$chartdata['cols'][] = array('label'=>'Domains','type'=>'number');
$chartdata['cols'][] = array('label'=>'Overall','type'=>'number');

$args = array();
$args['legendpos'] = 'right';

$reportdata["headertext"] = $chart->drawChart('Area',$chartdata,$args,'400px');
