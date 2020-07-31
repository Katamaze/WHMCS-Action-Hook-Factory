<?php

/**
 * Accept Quote without Logging In
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 *
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    if (in_array($vars['messagename'], array('Quote Delivery with PDF')))
    {
        if ($vars['mergefields']['quote_link'])
        {
            $data = Capsule::select(Capsule::raw('SELECT t1.id, t2.id AS clientid, t2.email FROM tblquotes AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['mergefields']['quote_number'] . '" LIMIT 1'))[0];

            $hash = strrev(md5($data->id . $data->clientid . $data->email)) . '-' . $data->id;
            $quote_link = (new SimpleXMLElement($vars['mergefields']['quote_link']))['href'];
            $url = parse_url($quote_link);
            $merge_fields['quote_link'] = str_replace($quote_link, $url['scheme'] . '://' . $url['host'] . '/index.php?qhash=' . $hash, $vars['mergefields']['quote_link']);

            return $merge_fields;
        }
    }
});

add_hook('ClientAreaHeadOutput', 1, function($vars)
{
    if ($_GET['qhash'])
    {
        $data = Capsule::select(Capsule::raw('SELECT t1.id, t1.subject, t2.id AS clientid, t2.firstname, t2.email FROM tblquotes AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . explode('-', $_GET['qhash'])[1] . '" AND stage != "Accepted" LIMIT 1'))[0];
        $hash = strrev(md5($data->id . $data->clientid . $data->email)) . '-' . $data->id;

        if ($hash === $_GET['qhash'])
        {
            $adminUsername = ''; // Optional for WHMCS 7.2 and later
            $results = localAPI('AcceptQuote', array('quoteid' => $data->id), $adminUsername);
            $results = localAPI('SendEmail', array('messagename' => 'Invoice Created', 'id' => $results['invoiceid']), $adminUsername);

            return <<<HTML
<script>
setTimeout(function()
{
    $("#modalAjax .modal-title").html('Quote #{$data->id} Accepted');
    $("#modalAjax .modal-body").html('<div class="container col-md-12"><div class="row"><div class="col-md-8"><h4>Hey, {$data->firstname}</h4><p>Thanks for accepting quote <strong>#{$data->id}</strong> ({$data->subject}). Here is what happens now:<ul><li>You will receive the invoice shortly</li><li>Once we receive your payment, we\'ll activate your order</li></ul>Please do not hesitate to <a href="contact.php"><strong>contact us</strong></a> if you have any questions.</p></div><div class="col-md-4 text-center"><p><a href="cart.php"><i class="fas fa-cart-plus fa-5x"></i></a></p><p><small>Keep browsing our Products</small></p><p><a href="cart.php" class="btn btn-info btn-block">Discover</a></p></div></div></div>');
    $('#modalAjax .loader').hide();
    $('#modalAjax .modal-submit').hide();
    $("#modalAjax").modal('show');
}, 250);
</script>
HTML;
        }
    }
});
