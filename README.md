# WHMCS as Fast as Possible

It's much easier to understand what is WHMCS making a parallelism with WordPress. Of the many free CMS, WordPress is the best and most popular solution to start a blog. WHMCS is the same in its reference market. It's the way go to [start an hosting business](https://katamaze.com/blog/38/starting-a-domain-and-hosting-company-in-2020) for **providers, web agencies and IT professionals**.

Both systems are the undisputed market leaders in their respective field. WordPress reaches 60% of market share. WHMCS attracts about 50.000 customers worldwide. They are both flexible and can accommodate several  businesses needs. In them there's more than a control panel and a blogging platform.

That said, similarities end here. WordPress is free, open source and good at many things. WHMCS kicks off at [15.95 $ per month](https://www.whmcs.com/pricing/). Source code is obfuscated and even if it is a solid platform, there are [some shortcomings](https://katamaze.com/blog/41/my-wishlist-for-whmcs-v8).

Continue reading our [beginners guide to WHMCS](https://katamaze.com/blog/23/what-is-whmcs-and-when-to-use-it-explained-for-beginners) for more details.

# Action Hooks

Action Hooks allow you to execute your own code when events occurr inside WHMCS. With them you can achieve impressive results. For example we managed to [transform WHMCS into a CMS](https://katamaze.com/whmcs/mercury/specifications) like WordPress with full support for [Search Engine Optimization](https://katamaze.com/blog/37/whmcs-seo-ways-to-improve-your-site-ranking-in-2020). We've also introduced new [billing concepts](https://katamaze.com/whmcs/billing-extension/specifications) (monthly invoicing, electronic invoicing, credit notes) and [Affiliate Marketing](https://katamaze.com/whmcs/commission-manager/specifications).

As you can see you can there's no limit to imagination. If you're new to WHMCS and Action Hook, please refer to the following articles:

* [Getting Started](https://developers.whmcs.com/hooks/getting-started/)
* [Hook Index](https://developers.whmcs.com/hooks/hook-index/)

# Perfect your WHMCS

Over the years we coded thousand of action hooks most of which are part of our [WHMCS modules](https://katamaze.com/whmcs).

In this repository we share a collection of action hooks for free that you can copy/paste on your WHMCS site. You can also adapt them to your specific needs or use as inspiration for your projects. We are continually adding and improving hooks so stay tuned.

# Action Hooks Collection

This [blog post](https://katamaze.com/blog/32/whmcs-action-hooks-collection-2020-updated-monthly) contains in-depth instructions and previews. The post is available also in [italian language](https://katamaze.it/blog/32/whmcs-action-hooks-collection-2020-updated-monthly). We are always willing to code new hooks based on your feedback so feel free to comment and ask for new ones. Let's start!

## Simulate / Run WHMCS Daily Cron Job on Demand

As the name suggests, WHMCS daily cron job runs once per day. There's no easy way to make it run multiple times. This could be frustrating in case you're coding or testing new features that's where this hook comes to help.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-live-demo.png)

The hook adds *Run Daily Cronjob* button (the orange one) on top of your WHMCS Administration. Clicking it allows to run WHMCS daily cron job whenever you want. All it takes is a click. Please, ignore *Reinstall* and *Manage Demo* buttons. We use them for [Live Demo](https://katamaze.com/demo) to let visitors try our modules before purchase.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/DailyCronJonOnDemand.php)

## Accept Quote without Logging In

When you send a quote, WHMCS forces customers to login in order to accept it. This hook allows them to accept without the need to login. Every time the *Quote Delivery with PDF* mail is sent, the hook overrides `{$quote_link}` with a new link that contains an hash that ensures the authenticity of the request. This way only the recipient can accept the quote.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/quote-accepted.png)

When the visitor clicks the link, the quote is automatically accepted and he/she sees the above modal on screen.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AcceptQuoteWithoutLogin.php)

## Bulk Auto Recalculate Client Domain & Products/Services

Yes, WHMCS integrates [Bulk Pricing Updater](https://docs.whmcs.com/Bulk_Pricing_Updater_Addon) but it works for all existing customers. Sometimes you simply need to recalculate prices for domains and products/services of a specific customer. This hook allows to do that in one click. First it adds the following button in client Summary.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-bulk-auto-recalculate-customer.png)

Second it shows this modal on screen where you can freely choose to auto-recalculate domains or products/services.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-bulk-auto-recalculate-customer-domain-product.png)

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/BulkAutoRecalculateClientDomainsProducts.php)

## cPanel & Plesk login button in My Services

Managing multiple hosting accounts could be frustrating for customers. The following hook makes things easier allowing them to login to any control panel directly from My Services list. Here's the preview.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-login-to-plesk-cpanel-from-service-list.png)

The hook works with any panel (cPanel, Plesk, DirectAdmin, Centova Cast...) provided that servers and products/services have been configured correctly. Before you get the code, keep in mind that this action hook requires some changes to two template files.

Open `templates/{YOUR_TEMPLATE}/clientareaproducts.tpl` and add the new *Manage* column in `thead` like follows.

```
<thead>
    <tr>
        <th>{$LANG.orderproduct}</th>
        <th>{$LANG.clientareaaddonpricing}</th>
        <th>{$LANG.clientareahostingnextduedate}</th>
        <th>{$LANG.clientareastatus}</th>
        <th>Manage</th>
        <th class="responsive-edit-button" style="display: none;"></th>
    </tr>
</thead>
```

Your `thead` could be slightly different (eg. your first column could be the SSL icon check) so change things accordingly. We suggest you to replace *Manage* with `$LANG` variable for multi-language support. Next move to `tbody` and add the cell right inside `{foreach}` loop.

```
<td class="text-center">
	{if $kt_autologin[$service.id]}
	<div class="btn-group btn-group-sm plesk-login" style="width:60px;">
		<a href="clientarea.php?action=productdetails&id={$service.id}&autologin=1" class="btn btn-primary btn-xs" alt="Click to Login" title="Click to Login" style="padding: 2px 5px;"><img src="templates/{$template}/img/katamaze_autologin/{$kt_autologin[$service.id]->type}.png" style="height:22px; max-width:39px"> <i class="fa fa-sign-in fa-fw" aria-hidden="true"></i></a>
	</div>
	{/if}
</td>
```

Now we need to disable sorting for the newly added column. On top of the file you'll find the following statement.

```
{include file="$template/includes/tablelist.tpl" tableName="ServicesList" noSortColumns="4" filterColumn="3"}
```

Focus on `noSortColumns="4"`. *4* means that the 5th column will be not sortable (column count start from zero). Change it accordingly. For example if your template uses the SSL check as 1st column, use `noSortColumns="0, 5"`.

Last but no least open `templates/{YOUR_TEMPLATE}/includes/head.tpl` and place this code at the very bottom.

```
{if $smarty.get.autologin}
<style>
body {
	visibility:hidden;
}
</style>
{/if}
```

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AutoLoginToAnyPanelFromMyServices.php)

## Auto-Terminate Free Trials After X Minutes

Free trials for a limited period is a good marketing strategy to capitalize on the leads you get. The problem with trials is that the smallest unit of time for WHMCS is the day meaning that for example you can't provide a trial for VPS that last for a couple of hours. WHMCS can't "think" for a period of less than a full day.

The following action hook allows to automatically terminate the given products/services after a certain number of minutes. It runs AfterCronJob hook point that normally triggers once every 5 minutes. Visit Setup > Automation Settings and make sure that cron.php runs every 5 minuts as suggested by WHMCS. The hook will do the rest. It also logs terminations in Activity Log.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AutoTerminateFreeTrialsAfterXMinutes.php)

## Stronger Password Generator for Auto-Provisioning

We give you not one, not two but three action hooks to override default passwords generated by WHMCS for service provisioning on third-party control panels like Plesk, cPanel, DirectAdmin and custom-made server modules.

* [v1](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/StrongerPasswordGeneratorForAutoProvisioning_v1.php) randomly picks 10 characters from `a-zA-Z0-9` and `!@#$%^&*()-=+?`
* [v2](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/StrongerPasswordGeneratorForAutoProvisioning_v2.php) same as above but makes sure that at least one special character is included in the password
* [v3](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/StrongerPasswordGeneratorForAutoProvisioning_v3.php) for extremely strong passwords. Individually define the number of digits, lowercase, uppercase and special characters to use. The resulting password will not use the same character twice

## New Clients as Affiliates

Automatically sets newly registered customers as Affiliates on WHMCS. This way they don't need to join manually.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/NewClientsAsAffiliates.php)

## Send Email & Add Reply on Ticket Status Change

When the status of a support ticket changes, WHMCS doesn't send any notification. We can tweak this process by sending an email and optionally also automatically add a reply to the ticket itself. This way you can guide customers through the resolving process letting them track the progress of tickets.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/SendEmailAndAddReplyOnTicketStatusChange.php)

## Client to Group based on Purchased Product/Service

Automatically assign a customer to a Client Group based on the product/service he/she has just purchased. The script triggers as soon as the order is accepted both manually and automatically.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AssignClientToGroupBasedOnPurchasedProduct.php)

## Prevent changes to Client Custom Fields

WHMCS has an in-built function to lock client profile fields you want to prevent clients being able to edit from clientarea (eg. email, company name). This feature however is not avaiable for client custom fields. Making such fields "disabled" via HTML is not an option. Anyone with bit of knowledge can skip this form of protection.

This hook acts as the last line of defense. It makes grants that no customer can submit changes. If necessary it can be enabled also for WHMCS Administrators.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/PreventChangesToClientCustomFields.php)

## Quote to Invoice conversion without redirect

If you are sending out a lot of quotes on a daily basis, the fact that WHMCS forces a redirect to the newly issued invoice could be frustrating. This hook prevents WHMCS from performing the redirect allowing you to keep woriking on the quote.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/QuoteToInvoiceNoRedirect.php)

## Remove/Hide Breadcrumb

WHMCS prements *Portal Home* to breadcrumb. There's nothing wrong with that but some people don't like it. This hook removes it from all WHMCS pages.

Bonus tip: if you don't want to use an action hook, you can use the following CSS. The result is the same.

```
.breadcrumb li:first-child {
    display:none;
}
.breadcrumb li:nth-child(2):before {
    content:" ";
}
```

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/RemovePortalHomeBreadcrumb.php)

## Knowledgebase Last Updated Date

WHMCS doesn't store *Last Updated* date when you edit Knowledgebase articles but you can retreive from Activity Log. It's not a stylish solution but it works. The hook adds `lastupdated` element to the existing `$kbarticle` Smarty array. Once done, change your KB template accordingly.

If you're looking for something more professional and up to date, learn how to benefit from [WHMCS SEO](https://katamaze.com/blog/37/whmcs-seo-ways-to-improve-your-site-ranking-in-2020) using [WHMCS as CMS](https://katamaze.com/whmcs/mercury).

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/KnowledgebaseLastUpdatedDate.php)

## Login as Client Language

Every time an administrator uses *Login as Client*, WHMCS overrides the default language of the selected customer with the one used by the administrator in WHMCS backend. This is bad because you're unknowingly changing the language for your customer. This also applies for languages that can't be used in clientarea.

Let's say your clientarea is in italian and you're using WHMCS backend in english. When you perform the *Login as Client*, WHMCS switches customer's language from italian to english and there's no way back. The customer in question is stucked with a language he cannot change. The following hook prevents that to happen.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/LoginAsClientPreserveLanguage.php)

## Prevent Emails to be sent based on Client Group

This hook prevents WHMCS from sending *General Messages* email templats to specific client groups based on a sort of blacklist.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/PreventEmailSendingBasedOnClientGroup.php)



