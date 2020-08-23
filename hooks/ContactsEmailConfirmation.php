<?php

// WORK IN PROGRESS - THE HOOK IS NOT FUNCTIONAL ATM

/**
 * Require Email Confirmation for Contacts
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('ContactAdd', 1, function($vars)
{
    if (!Capsule::schema()->hasTable('kt_contacts'))
    {
        Capsule::select(Capsule::raw('CREATE TABLE `kt_contacts` (`id` int(11) NOT NULL, `userid` int(11) NOT NULL, `data` text COLLATE utf8_unicode_ci NOT NULL, `created_at` date NOT NULL DEFAULT current_timestamp()) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
        Capsule::select(Capsule::raw('ALTER TABLE `kt_contacts` ADD PRIMARY KEY (`id`);'));
    }

    $userID = $vars['userid'];
    $contactID = $vars['contactid'];
    unset($vars['userid'], $vars['contactid']);

    Capsule::table('kt_contacts')->insert(array('id' => $contactID, 'userid' => $userID, 'data' => json_encode($vars)));
    Capsule::table('tblcontacts')->where('id', $contactID)->delete();
});

add_hook('AdminAreaHeadOutput', 1, function($vars)
{
    if ($vars['filename'] == 'clientscontacts' AND $_GET['userid'])
    {
        $pendingContacts = Capsule::table('kt_contacts')->where('userid', $_GET['userid'])->get();

        if ($pendingContacts)
        {
            $total = count($pendingContacts);
            $total = ($total == '1' ? $total . ' contact' : $total . ' contacts');

            foreach ($pendingContacts as $contact)
            {
                $data = json_decode($contact->data);
                $email = $data->email;
                unset($data->email);

                foreach ($data as $k => $v)
                {
                    if ($v)
                    {
                        $temp .= $k . ': ' . $v . '<br>';
                    }
                }

                $table .= '<tr><td>' . $email . '</td><td><small>' . $temp . '</small></td><td>' . $contact->created_at . '</td><td><input type="checkbox" name="contactIDs[' . $contact->id . ']"></td></tr>';
            }

            return <<<HTML
<script>
$(document).ready(function(){
    $('select[name="contactid"]').after(('<span class="alert alert-warning pull-right" style="padding:5px"><i class="far fa-pause-circle"></i> <a href="#" id="expandContacts"><strong>{$total}</strong></a> awaiting confirmation</span>'));

    $("#expandContacts").click(function() {
        $("#modalAjax .modal-header").html('Contacts with Unconfirmed Emails');
        $("#modalAjax .modal-body").html('<table class="datatable" style="width:100%"><tr><th>Email</th><th>Data</th><th>Date</th><th width="20"></th></tr>{$table}</table>');
        $('#modalAjax .loader').hide();
        $('#modalAjax .modal-submit').html('Mark as Confirmed');
        $("#modalAjax").modal('show');
    });
})
</script>
HTML;
        }
    }
});
