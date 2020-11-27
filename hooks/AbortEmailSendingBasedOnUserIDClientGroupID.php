<?php

/**
 * Abort the sending of email templates based on User ID and/or Client Group ID
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('EmailPreSend', 1, function($vars)
{
    $disallowedEmailTemplates = array('Invoice Created'); // The name of the email template being sent
    $disallowedClientGroups = array('4'); // Affected Client Group ID
    $disallowedUserIDs = array('5'); // Affected User ID
    $removePDFAttachments = false; // Set true to send emails to the selected users as normal but remove PDF invoice attachments

    switch (Capsule::table('tblemailtemplates')->select('type')->where('name', $vars['messagename'])->first()->type)
    {
        case 'affiliate': $user = $vars['relid']; break;
        case 'domain': $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid FROM tbldomains AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        case 'general': $user = $vars['relid']; break;
        case 'invoice': $attachments = true; $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid, t2.language FROM tblinvoices AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        case 'product': $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid FROM tblhosting AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        case 'support': $user = Capsule::select(Capsule::raw('SELECT t2.id, t2.groupid FROM tbltickets AS t1 LEFT JOIN tblclients AS t2 ON t1.userid = t2.id WHERE t1.id = "' . $vars['relid'] . '" LIMIT 1'))[0]; break;
        default: return; break;
    }

    if (in_array($user->groupid, $disallowedClientGroups) OR in_array($user->id, $disallowedUserIDs))
    {
        $abortSend = true;
    }

    if ($removePDFAttachments AND $attachments AND $abortSend)
    {
        if (file_exists(ROOTDIR . '/vendor/phpmailer/phpmailer/PHPMailerAutoload.php'))
        {
            require_once( ROOTDIR . '/vendor/phpmailer/phpmailer/PHPMailerAutoload.php');

            foreach (Capsule::select(Capsule::raw('SELECT setting, value FROM tblconfiguration WHERE setting IN ("CompanyName", "Email", "MailType", "SMTPHost", "SMTPUsername", "SMTPPassword", "SMTPPort", "SMTPSSL")')) as $v)
            {
                if ($v->setting == 'SMTPPassword' AND $v->value): $v->value = Decrypt($v->value); endif;
                $conf[$v->setting] = $v->value;
            }

            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';

            try
            {
                if ($conf->MailType == 'smtp')
                {
                    $mail->IsSMTP();
                    $mail->Host       = $conf['SMTPHost'];
                    $mail->SMTPAuth   = true;
                    $mail->SMTPSecure = $conf['SMTPSSL'];
                    $mail->Port       = $conf['SMTPPort'];
                    $mail->Username   = $conf['SMTPUsername'];
                    $mail->Password   = $conf['SMTPPassword'];
                    $mail->Mailer     = 'smtp';
                    $mail->CharSet    = 'UTF-8';
                }
                else
                {
                    $mail->IsMail();
                    $mail->CharSet    = 'UTF-8';
                }

                $emailTemplate = Capsule::select(Capsule::raw('SELECT subject, message FROM tblemailtemplates WHERE name = "' . $vars['messagename'] . '" AND language = "' . $user->language . '" LIMIT 1'))[0];

                foreach (array('invoice_html_contents', 'client_name', 'invoice_date_created', 'invoice_payment_method', 'invoice_num', 'invoice_total', 'invoice_date_due', 'signature') as $v)
                {
                    $emailTemplate->subject = str_replace('{$' . $v . '}', $vars['mergefields'][$v], $emailTemplate->subject);
                    $emailTemplate->message = str_replace('{$' . $v . '}', $vars['mergefields'][$v], $emailTemplate->message);
                }

                $mail->AddAddress($vars['mergefields']['client_email'], $vars['mergefields']['client_name']);
                $mail->SetFrom($conf['Email'], $conf['CompanyName']);
                $mail->Subject = $emailTemplate->subject;
                $mail->MsgHTML($emailTemplate->message);
                $mail->Send();
                $mail->ClearAllRecipients();
            }
            catch (phpmailerException $e)
            {
                //echo $e->errorMessage(); // Pretty error
            }
            catch (Exception $e)
            {
                //echo $e->getMessage(); // Boring error
            }
        }
    }

    if ($abortSend)
    {
        return array('abortsend' => true);
    }
});
