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

if (substr($GLOBALS['CONFIG']['Version'], 0, 1) === '8') : $v8 = true;
endif;
$group_id = 'all';
$limit_checking = 20;
if (array_key_exists('group_id', $_GET) && $_GET['group_id'] != '') {
    $group_id = $_GET['group_id'];
}

$dateFilter = Carbon::create($year, $month, 1);
$startOfMonth = $dateFilter->startOfMonth()->toDateTimeString();
$endOfMonth = $dateFilter->endOfMonth()->toDateTimeString();

$command = 'GetProductsGroups';
$postData = array('local' => true);

$groups = localAPI($command, $postData);

$y_select = '<form action="' . $_SERVER['PHP_SELF'] . '" method="get">';
$y_select .= '<input type="hidden" name="report" value="WebHS_Churn_Rate">';
$y_select .= '<table class="form" width="100%" cellspacing="2" cellpadding="3" border="0"><tbody>
   <tr>
      <td class="fieldlabel" width="20%">Choose a Group</td>
      <td class="fieldarea">';
$y_select .= '<select class="form-control select-inline" name="group_id" id="group_id">';
$y_select .= '<option value="all" ' . ($group_id == 'all' ? "selected" : "") . '>All</option>';
foreach ($groups['groups'] as $k => $group) {
    $y_select .= '<option value="' . $group->id . '" ' . ($group_id == $group->id ? "selected" : "") . '>' . $group->name . '</option>';
}
$y_select .= '</select>';
$y_select .= '</td>
   </tr>
   <tr>
      <td class="fieldlabel" width="20%"></td>
      <td class="fieldarea"><input type="submit" class="btn btn-primary"/></td>
   </tr>
</tbody></table>';
$y_select .= '</form><br>';

$reportdata['title'] = 'Churn Rate for ' . $year;
$reportdata['description'] = 'Rate at which customers stop doing business with you.' . $y_select;
$reportdata['yearspagination'] = true;
if ($group_id == 'all') {

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
} else {
    $reportdata['tableheadings'] = array(
        'Date',
        'Products',
        '<strong class="text-success"><i class="far fa-plus-square"></i></strong>',
        '<strong class="text-danger"><i class="far fa-minus-square"></i></strong>',
        '<strong><i class="fas fa-percentage"></i></strong>',
        'Overall',
        '<strong class="text-success"><i class="far fa-plus-square"></i></strong>',
        '<strong class="text-danger"><i class="far fa-minus-square"></i></strong>',
        '<strong><i class="fas fa-percentage"></i></strong>',
    );
}
$reportvalues = array();
$mothMatrix = array('1' => '0', '2' => '0', '3' => '0', '4' => '0', '5' => '0', '6' => '0', '7' => '0', '8' => '0', '9' => '0', '10' => '0', '11' => '0', '12' => '0');

$groups_search = [];

if ($group_id != 'all') {
    $groups_search = [];
    $query = Capsule::table('tblproducts')
        ->select('id');

    $Productgroups = $query->where('gid', $group_id)
        ->get()->toArray();

    foreach ($Productgroups as $k => $prod) {
        $groups_search[] = $prod->id;
    }
}


// Products/Services
$groupBy = Capsule::raw('date_format(`regdate`, "%c")');
if ($group_id != 'all') {//IF A GROUP IS SELECTED
    $products['active']['previousYears'] = Capsule::table('tblhosting')->whereYear('regdate', '<=', $year - 1)->whereYear('nextduedate', '>=', $year)->whereIn('packageid', $groups_search)->orWhereNull('packageid')->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->whereNotIn('domainstatus', ['Pending', 'Fraud'])->pluck(Capsule::raw('count(id) as total'))[0];
    $products['active']['currentYear'] = Capsule::table('tblhosting')->whereYear('regdate', '=', $year)->whereYear('nextduedate', '>=', $year)->whereIn('packageid', $groups_search)->orWhereNull('packageid')->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->whereNotIn('domainstatus', ['Pending', 'Fraud'])->groupBy($groupBy)->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`regdate`, "%c") as month'));
} else {
    $products['active']['previousYears'] = Capsule::table('tblhosting')->whereYear('regdate', '<=', $year - 1)->whereYear('nextduedate', '>=', $year)->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->whereNotIn('domainstatus', ['Pending', 'Fraud'])->pluck(Capsule::raw('count(id) as total'))[0];
    $products['active']['currentYear'] = Capsule::table('tblhosting')->whereYear('regdate', '=', $year)->whereYear('nextduedate', '>=', $year)->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->whereNotIn('domainstatus', ['Pending', 'Fraud'])->groupBy($groupBy)->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`regdate`, "%c") as month'));
}

if ($v8) : $products['active']['currentYear'] = $products['active']['currentYear']->all();
endif;
$products['active']['currentYear'] = $products['active']['currentYear'] + $mothMatrix;
ksort($products['active']['currentYear']);
$products['active']['total'] = $products['active']['previousYears'] + array_sum($products['active']['currentYear']);
$groupBy = Capsule::raw('date_format(`nextduedate`, "%c")');
if ($group_id != 'all') {//IF A GROUP IS SELECTED
    $products['terminated'] = Capsule::table('tblhosting')->whereYear('nextduedate', '=', $year)->whereIn('packageid', $groups_search)->orWhereNull('packageid')->whereIn('domainstatus', ['Suspended', 'Terminated', 'Cancelled', 'Suspended'])->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%c") as month'));
} else {
    $products['terminated'] = Capsule::table('tblhosting')->whereYear('nextduedate', '=', $year)->whereIn('domainstatus', ['Suspended', 'Terminated', 'Cancelled', 'Suspended'])->whereNotIn('billingcycle', ['One Time', 'Completed', 'Free Account'])->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%c") as month'));
}
if ($v8) : $products['terminated'] = $products['terminated']->all();
endif;
$products['terminated'] = $products['terminated'] + $mothMatrix;
ksort($products['terminated']);
$products['variation'] = array_map('subtract', $products['active']['currentYear'], $products['terminated']);
$products['variation'] = array_combine(range(1, count($products['variation'])), array_values($products['variation']));
if ($group_id == 'all') {
    // Domains
    $groupBy = Capsule::raw('date_format(`registrationdate`, "%c")');
    $domains['active']['previousYears'] = Capsule::table('tbldomains')->whereYear('registrationdate', '<=', $year - 1)->whereYear('nextduedate', '>=', $year)->whereNotIn('status', ['Pending', 'Pending Registration', 'Pending Transfer', 'Fraud'])->pluck(Capsule::raw('count(id) as total'))[0];
    $domains['active']['currentYear'] = Capsule::table('tbldomains')->whereYear('registrationdate', '=', $year)->whereYear('nextduedate', '>=', $year)->whereNotIn('status', ['Pending', 'Pending Registration', 'Pending Transfer', 'Fraud'])->groupBy($groupBy)->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`registrationdate`, "%c") as month'));
    if ($v8) : $domains['active']['currentYear'] = $domains['active']['currentYear']->all();
    endif;
    $domains['active']['currentYear'] = $domains['active']['currentYear'] + $mothMatrix;
    ksort($domains['active']['currentYear']);
    $domains['active']['total'] = $domains['active']['previousYears'] + array_sum($domains['active']['currentYear']);
    $groupBy = Capsule::raw('date_format(`nextduedate`, "%c")');
    $domains['terminated'] = Capsule::table('tbldomains')->whereYear('nextduedate', '=', $year)->whereIn('status', ['Grace', 'Redemption', 'Expired', 'Transferred Away', 'Cancelled'])->groupBy($groupBy)->orderBy('nextduedate')->pluck(Capsule::raw('count(id) as total'), Capsule::raw('date_format(`nextduedate`, "%c") as month'));
    if ($v8) : $domains['terminated'] = $domains['terminated']->all();
    endif;
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
}

for ($tmonth = 1; $tmonth <= 12; $tmonth++) {
    if (date('Y') == $year and sprintf("%02d", $tmonth) > $month) : continue;
    endif;

    $date = Carbon::create($year, $tmonth, 1);
    $dateMonthYear = $date->format('M Y');
    $dateMonth = $date->format('M');

    // Products
    if ($tmonth == '1') {
        $products['start'][$tmonth] = $products['active']['previousYears'] + $products['start'][$tmonth];
        $products['end'][$tmonth] = $products['start'][$tmonth] + $products['variation'][$tmonth];
    } else {
        $products['start'][$tmonth] = $products['end'][$tmonth - 1];
        $products['end'][$tmonth] = $products['start'][$tmonth] + $products['variation'][$tmonth];
    }
    if ($group_id == 'all') {

        // Domains
        if ($tmonth == '1') {
            $domains['start'][$tmonth] = $domains['active']['previousYears'] + $domains['start'][$tmonth];
            $domains['end'][$tmonth] = $domains['start'][$tmonth] + $domains['variation'][$tmonth];
        } else {
            $domains['start'][$tmonth] = $domains['end'][$tmonth - 1];
            $domains['end'][$tmonth] = $domains['start'][$tmonth] + $domains['variation'][$tmonth];
        }
    }
    $productsChurnRate = number_format(($products['terminated'][$tmonth] / $products['start'][$tmonth]) * 100, 1, '.', '') + 0;

    if ($group_id == 'all') {
        $domainsChurnRate = number_format(($domains['terminated'][$tmonth] / $domains['start'][$tmonth]) * 100, 1, '.', '') + 0;
    }

    if (sprintf("%02d", $tmonth) > $month and date('Y') == $year) {
        $dateMonthYear = '<span data-toggle="tooltip" data-placement="top" title="" data-original-title="Statistics for current month are inaccurate as renewals still have to occur">' . $dateMonthYear . ' <i class="fas fa-info-circle" style="opacity:0.8;"></i></span>';
    }

    if ($group_id == 'all') {

        $reportdata['tablevalues'][] = array(
            $dateMonthYear,
            formatCell(array('col' => 'products=', 'variation' => $products['variation'][$tmonth], 'start' => $products['start'][$tmonth], 'end' => $products['end'][$tmonth])),
            formatCell(array('col' => 'products+', 'increase' => $products['active']['currentYear'][$tmonth])),
            formatCell(array('col' => 'products-', 'decrease' => $products['terminated'][$tmonth])),
            formatCell(array('col' => 'products%', 'churnRate' => $productsChurnRate)),
            formatCell(array('col' => 'domains=', 'variation' => $domains['variation'][$tmonth], 'start' => $domains['start'][$tmonth], 'end' => $domains['end'][$tmonth])),
            formatCell(array('col' => 'domains+', 'increase' => $domains['active']['currentYear'][$tmonth])),
            formatCell(array('col' => 'domains-', 'decrease' => $domains['terminated'][$tmonth])),
            formatCell(array('col' => 'domains%', 'churnRate' => $domainsChurnRate)),
            formatCell(array('col' => 'overall=', 'variation' => $products['variation'][$tmonth] + $domains['variation'][$tmonth], 'start' => $products['start'][$tmonth] + $domains['start'][$tmonth], 'end' => $products['end'][$tmonth] + $domains['end'][$tmonth])),
            formatCell(array('col' => 'overall+', 'increase' => $products['active']['currentYear'][$tmonth] + $domains['active']['currentYear'][$tmonth])),
            formatCell(array('col' => 'overall-', 'decrease' => $products['terminated'][$tmonth] + $domains['terminated'][$tmonth])),
            formatCell(array('col' => 'overall%', 'churnRate' => $productsChurnRate + $domainsChurnRate))
        );
    } else {
        $reportdata['tablevalues'][] = array(
            $dateMonthYear,
            formatCell(array('col' => 'products=', 'variation' => $products['variation'][$tmonth], 'start' => $products['start'][$tmonth], 'end' => $products['end'][$tmonth])),
            formatCell(array('col' => 'products+', 'increase' => $products['active']['currentYear'][$tmonth])),
            formatCell(array('col' => 'products-', 'decrease' => $products['terminated'][$tmonth])),
            formatCell(array('col' => 'products%', 'churnRate' => $productsChurnRate)),
            formatCell(array('col' => 'overall=', 'variation' => $products['variation'][$tmonth], 'start' => $products['start'][$tmonth], 'end' => $products['end'][$tmonth])),
            formatCell(array('col' => 'overall+', 'increase' => $products['active']['currentYear'][$tmonth])),
            formatCell(array('col' => 'overall-', 'decrease' => $products['terminated'][$tmonth])),
            formatCell(array('col' => 'overall%', 'churnRate' => $productsChurnRate))
        );
    }
    if ($group_id == 'all') {

        $chartdata['rows'][] = array(
            'c' => array(
                array('v' => $dateMonth),
                array('v' => (int)$products['end'][$tmonth]),
                array('v' => (int)$domains['end'][$tmonth]),
                array('v' => (int)$products['end'][$tmonth] + $domains['end'][$tmonth]),
            )
        );
    } else {
        $chartdata['rows'][] = array(
            'c' => array(
                array('v' => $dateMonth),
                array('v' => (int)$products['end'][$tmonth]),
            )
        );
    }
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

    if (in_array($data['col'], array('products+', 'domains+', 'overall+'))) {
        if ($data['increase']) {
            return '<span>' . $data['increase'] . '</span>';
        } else {
            return '-';
        }
    } elseif (in_array($data['col'], array('products-', 'domains-', 'overall-'))) {
        if ($data['decrease']) {
            return '<span>' . $data['decrease'] . '</span>';
        } else {
            return '-';
        }
    } elseif (in_array($data['col'], array('products=', 'domains=', 'overall='))) {
        if ($data['variation'] > '0') {
            $variation = '<small class="pull-right" style="opacity:0.8;">' . abs($data['variation']) . '<i class="fa fa-angle-double-up fa-fw text-success"></i></small>';
        } elseif ($data['variation'] < '0') {
            $variation = '<small class="pull-right" style="opacity:0.8;">' . abs($data['variation']) . '<i class="fa fa-angle-double-down fa-fw text-danger"></i></small>';
        }

        if ($data['start'] != $data['end']) {
            return $data['start'] . ' <i class="fas fa-angle-right fa-fw"></i> ' . $data['end'] . $variation;
        } else {
            return $data['start'];
        }
    } elseif (in_array($data['col'], array('products%', 'domains%', 'overall%'))) {
        if ($data['churnRate'] > 0) {
            return '<span class="label label-danger">' . $data['churnRate'] . '%</span>';
        } else {
            return '-';
        }
    }
}

function subtract($a, $b)
{
    return $a - $b;
}

$chartdata['cols'][] = array('label' => 'Day', 'type' => 'string');
$chartdata['cols'][] = array('label' => 'Products', 'type' => 'number');
if ($group_id == 'all') {
    $chartdata['cols'][] = array('label' => 'Domains', 'type' => 'number');
    $chartdata['cols'][] = array('label' => 'Overall', 'type' => 'number');
}

$args = array();
$args['legendpos'] = 'right';

$reportdata["headertext"] = $chart->drawChart('Area', $chartdata, $args, '400px');
