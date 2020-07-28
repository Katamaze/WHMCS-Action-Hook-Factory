<?php

/**
 * One-off Products/Services & Domain purchase require Product/Service
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    $onetimeProducts = array('1'); // Array of product IDs to treat as "one-off" (customer is not allowed to order the same product multiple times)
    $onetimeProductGroups = array('1'); // Same as above but for product group IDs. All producs inside such groups are treated as one-off
    $firstTimerTollerance = true; // Product-based restrictions are disabled for new customers placing their first order with you
    $notRepeatable = false; // If a customer already has a one-off product, he can't purchase further one-offs ($firstTimerTollerance is ignored)
    $domainRequiresProduct = false; // Domain purchase is allowed only if any of the following conditions is met: a) Customer has an existing product/service (`Pending` and `Terminated` don't count) b) Customer is purchasing a domain and a product/service

    if ($_SESSION['cart']['products'] AND ($onetimeProductGroups OR $onetimeProducts))
    {
        $disallowedPids = Capsule::table('tblproducts')->whereIn('gid', $onetimeProductGroups)->orWhereIn('id', $onetimeProducts)->pluck('id');
        $productsInCart = array_column($_SESSION['cart']['products'], 'pid');

        if ($_SESSION['uid'])
        {
            $userProducts = Capsule::table('tblhosting')->where('userid', '=', $_SESSION['uid'])->WhereIn('packageid', $disallowedPids)->groupBy('packageid')->pluck('packageid');
        }

        if ($notRepeatable)
        {
            $groupByProducts = array_count_values($productsInCart);
            $groupByProductsKeys = array_keys($groupByProducts);
            $i = 1;

            foreach ($_SESSION['cart']['products'] as $k => $v)
            {
                if (in_array($v['pid'], $groupByProductsKeys) AND in_array($v['pid'], $disallowedPids))
                {
                    if ($i > 1)
                    {
                        $removedFromCart = true;
                        unset($_SESSION['cart']['products'][count($_SESSION['cart']['products'])-1]);
                    }

                    $i++;
                }
            }
        }
        elseif (!$firstTimerTollerance)
        {
            foreach ($productTotals as $k => $v)
            {
                if ($v > 1)
                {
                    $removedFromCart = true;
                    unset($_SESSION['cart']['products'][count($_SESSION['cart']['products'])-1]);
                }
            }
        }

        foreach ($_SESSION['cart']['products'] as $k => $v)
        {
            if (in_array($v['pid'], $userProducts))
            {
                $removedFromCart = true;
                unset($_SESSION['cart']['products'][$k]);
            }

            $productTotals[$v['pid']]++;
        }

        if ($removedFromCart)
        {
            header('Location: cart.php?a=view&disallowed=1');
            die();
        }
    }

    if ($_SESSION['cart']['domains'] AND $domainRequiresProduct)
    {
        $userHasProduct = Capsule::table('tblhosting')->where('userid', '=', $_SESSION['uid'])->whereNotIn('domainstatus', array('Pending', 'Terminated'))->pluck('id');

        if (!$userHasProduct AND !$_SESSION['cart']['products'])
        {
            unset($_SESSION['cart']['domains']);
            header('Location: cart.php?a=view&requireProduct=1');
            die();
        }
    }
});

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    $promptRemoval = 'js-alert'; // Choose one of the following options: "bootstrap-alert", "modal", "js-alert" (works on Six template. Change jQuery selectors accordingly for custom templates)
    $textDisallowed = 'The Product/Service can be purchased only once.'; // Don't forget to "\" escape
    $textRequireProduct = 'Domain purchase require an active Product/Service.'; // Don't forget to "\" escape

    if ($vars['filename'] == 'cart' AND $_GET['a'] == 'view')
    {
        if ($_GET['disallowed']): $text = $textDisallowed;
        elseif ($_GET['requireProduct']): $text = $textRequireProduct; endif;

        if ($promptRemoval == 'bootstrap-alert')
        {
            $code = <<<HTML
$("form[action='/cart.php?a=view']").prepend('<div class="alert alert-warning text-center" role="alert">{$text}</div>');
HTML;
        }
        elseif ($promptRemoval == 'modal')
        {
            $code = <<<HTML
$("#modalAjax .modal-header").hide();
$("#modalAjax .modal-body").html('<div class="text-center" style="padding:15px"><i class="far fa-grin-beam-sweat fa-5x"></i></div><div class="text-center" style="padding:15px">{$text}</div>');
$('#modalAjax .loader').hide();
$('#modalAjax .modal-submit').hide();
$("#modalAjax").modal('show');
HTML;
        }
        elseif ($promptRemoval == 'js-alert')
        {
            $code = <<<HTML
alert('{$text}');
HTML;
        }

        if ($_GET['disallowed'] OR $_GET['requireProduct'])
        {
            return <<<HTML
<script type="text/javascript">
$(document).ready(function() {
    {$code}
});
</script>
HTML;
        }
    }
});
