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
    $onetimeProducts = array(); // The following Product/Service IDs are treated as "one-off" (each customer can purchase them only once)
    $onetimeProductGroups = array(); // Same as above but works on Product Group IDs ("one-off" concept extends to all products/services in such groups)
    $domainRequiresProduct = false; // Allow domain purchase only when if any of the following conditions is met: a) Customer has an existing Product/Service (Pending and Terminated don't count) b) Customer is purchasing a domain and a Product/Service

    if ($_SESSION['uid'])
    {
        if ($_SESSION['cart']['products'] AND ($onetimeProductGroups OR $onetimeProducts))
        {
            $disallowedPids = Capsule::table('tblproducts')->whereIn('gid', $onetimeProductGroups)->orWhereIn('id', $onetimeProducts)->pluck('id');
            $userProducts = Capsule::table('tblhosting')->where('userid', '=', $_SESSION['uid'])->WhereIn('packageid', $disallowedPids)->groupBy('packageid')->pluck('packageid');

            foreach ($_SESSION['cart']['products'] as $k => $v)
            {
                if (in_array($v['pid'], $userProducts))
                {
                    $removedFromCart = true;
                    unset($_SESSION['cart']['products'][$k]);
                }
            }

            if ($removedFromCart)
            {
                header('Location: cart.php?a=view&disallowed=1');
                die();
            }
        }
        elseif ($_SESSION['cart']['domains'] AND $domainRequiresProduct)
        {
            $userHasProduct = Capsule::table('tblhosting')->where('userid', '=', $_SESSION['uid'])->whereNotIn('domainstatus', array('Pending', 'Terminated'))->pluck('id');

            if (!$userHasProduct AND !$_SESSION['cart']['products'])
            {
                unset($_SESSION['cart']['domains']);
                header('Location: cart.php?a=view&requireProduct=1');
                die();
            }
        }
    }
});

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    if ($_SESSION['uid'] AND $vars['filename'] == 'cart' AND $_GET['a'] == 'view')
    {
        if ($_GET['disallowed'])
        {
            return <<<HTML
<script type="text/javascript">
$(document).ready(function() {
    $("form[action='/cart.php?a=view']").prepend('<div class="alert alert-warning text-center" role="alert">The Product/Service can be purchased only once.</div>');
});
</script>
HTML;
        }
        elseif ($_GET['requireProduct'])
        {
            return <<<HTML
<script type="text/javascript">
$(document).ready(function() {
    $("form[action='/cart.php?a=view']").prepend('<div class="alert alert-warning text-center" role="alert">Domain purchase require an active Product/Service.</div>');
});
</script>
HTML;
        }
    }
});
