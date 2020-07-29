#### Table of Contents
- [WHMCS as Fast as Possible](#whmcs-as-fast-as-possible)
- [Understanding Action Hooks](#understanding-action-hooks)
- [Perfect Your WHMCS](#perfect-your-whmcs)
- [Free Hooks Collection](#free-hooks-collection)

# WHMCS as Fast as Possible

It's much easier to understand what is WHMCS making a parallelism with WordPress. Of the many free CMS, WordPress is the best and most popular solution to start a blog. WHMCS is the same in its reference market. It's the way go to [start an hosting business](https://katamaze.com/blog/38/starting-a-domain-and-hosting-company-in-2020) for **providers, web agencies and IT professionals**.

Both systems are the undisputed market leaders in their respective field. WordPress reaches 60% of market share. WHMCS attracts about 50.000 customers worldwide. They are both flexible and can accommodate several  businesses needs. In them there's more than a control panel and a blogging platform.

That said, similarities end here. WordPress is free, open source and good at many things. WHMCS kicks off at [15.95 $ per month](https://www.whmcs.com/pricing/). Source code is obfuscated and even if it is a solid platform, there are [some shortcomings](https://katamaze.com/blog/41/my-wishlist-for-whmcs-v8).

Continue reading our [beginners guide to WHMCS](https://katamaze.com/blog/23/what-is-whmcs-and-when-to-use-it-explained-for-beginners) and [common mistakes to avoid in WHMCS](https://katamaze.com/blog/9/the-most-common-whmcs-mistakes-to-avoid) for more details.

# Understanding Action Hooks

Action Hooks allow you to execute your own code when events occurr inside WHMCS. With them you can achieve impressive results.

For example we managed to [transform WHMCS into a CMS](https://katamaze.com/whmcs/mercury/specifications) like WordPress with full support for [Search Engine Optimization](https://katamaze.com/blog/37/whmcs-seo-ways-to-improve-your-site-ranking-in-2020). We've also introduced new [billing concepts](https://katamaze.com/whmcs/billing-extension/specifications) (monthly invoicing, electronic invoicing, credit notes) and [Affiliate Marketing](https://katamaze.com/whmcs/commission-manager/specifications).

As you can see you can there's no limit to imagination. If you're new to WHMCS and Action Hooks, please refer to the following docs:

* [Getting Started](https://developers.whmcs.com/hooks/getting-started/)
* [Hook Index](https://developers.whmcs.com/hooks/hook-index/)

# Perfect Your WHMCS

Over the years we coded thousand of action hooks most of which are part of our [WHMCS modules](https://katamaze.com/whmcs).

In this repository we share a collection of action hooks for free that you can copy/paste on your WHMCS site. You can also adapt them to your specific needs or use as inspiration for your projects.

The point of this this project is to help **Developers**, **Hosting Providers**, **Web Agencies** and **IT professionals** to perfect WHMCS. We are continually adding and improving hooks. It would be great if you could join us!

* [Request new Action Hooks](https://katamaze.com/blog/32/whmcs-action-hooks-collection-2020-updated-monthly) posting a comment
* [Contributing Guidelines](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/CONTRIBUTING.md) (for Developers)
* [Report a Bug](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/issues)

# Free Hooks Collection

This [blog post](https://katamaze.com/blog/32/whmcs-action-hooks-collection-2020-updated-monthly) contains in-depth instructions and previews. The post is available also in [italian language](https://katamaze.it/blog/32/whmcs-action-hooks-collection-2020-updated-monthly). We are always willing to code new hooks based on your feedback so feel free to [comment](https://katamaze.com/blog/32/whmcs-action-hooks-collection-2020-updated-monthly) and ask for new ones.

Scripts are provided free of charge "as is" without warranty of any kind. **You're not allowed to remove copyright notice**. Let's start!

* [Simulate / Run WHMCS Daily Cron Job on Demand](#simulate--run-whmcs-daily-cron-job-on-demand)
* [Accept Quote without Logging In](#accept-quote-without-logging-in)
* [Bulk Auto Recalculate Client Domain & Products/Services](#bulk-auto-recalculate-client-domain--productsservices)
* [cPanel & Plesk login button in My Services](#cpanel--plesk-login-button-in-my-services)
* [Related Service in Ticket Sidebar](#related-service-in-ticket-sidebar)
* [Force Payment Gateway depending on Invoice Balance](#force-payment-gateway-depending-on-invoice-balance)
* [Auto-Terminate Free Trials After X Minutes](#auto-terminate-free-trials-after-x-minutes)
* [Stronger Password Generator for Auto-Provisioning](#stronger-password-generator-for-auto-provisioning)
* [One-off Products/Services & Domain purchase require Product/Service](#one-off-productsservices--domain-purchase-require-productservice)
* [New Clients as Affiliates](#new-clients-as-affiliates)
* [Send Email & Add Reply on Ticket Status Change](#send-email--add-reply-on-ticket-status-change)
* [Client to Group based on Purchased Product/Service](#client-to-group-based-on-purchased-productservice)
* [Prevent changes to Client Custom Fields](#prevent-changes-to-client-custom-fields)
* [Quote to Invoice conversion without redirect](#quote-to-invoice-conversion-without-redirect)
* [Remove/Hide Breadcrumb](#removehide-breadcrumb)
* [Knowledgebase Last Updated Date](#knowledgebase-last-updated-date)
* [Login as Client Language](#login-as-client-language)
* [Prevent Emails to be sent based on Client Group](#prevent-emails-to-be-sent-based-on-client-group)
* [Abort Auto-Provisioning when there's a Note in the Order](#abort-auto-provisioning-when-theres-a-note-in-the-order)
* [Add Button next to Module's Functions](#add-button-next-to-modules-functions)
* [Announcements Meta Description](#announcements-meta-description)
* [Promotion Code in Email Template](#promotion-code-in-email-template)
* [Automatically Accept Order when Invoice is Paid](#automatically-accept-order-when-invoice-is-paid)
* [Hide Google Invisible reCAPTCHA Badge](#hide-google-invisible-recaptcha-badge)
* [Chatstack Disable for Logged-In Users and Administrators](#chatstack-disable-for-logged-in-users-and-administrators)
* [Notify Fradulent Orders](#notify-fradulent-orders)

## Simulate / Run WHMCS Daily Cron Job on Demand

As the name suggests, WHMCS daily cron job runs once per day. There's no easy way to make it run multiple times. This could be frustrating in case you're coding or testing new features that's where this hook comes to help.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-live-demo.png)

The hook adds *Run Daily Cronjob* button (the orange one) on top of your WHMCS Administration. Clicking it allows to run WHMCS daily cron job whenever you want. All it takes is a click. Please, ignore *Reinstall* and *Manage Demo* buttons. We use them for [Live Demo](https://katamaze.com/demo) to let visitors try our modules before purchase.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/DailyCronJonOnDemand.php)

## Accept Quote without Logging In

When you send a quote, WHMCS forces customers to login in order to accept it. This hook allows them to accept without the need to login. Every time the *Quote Delivery with PDF* mail is sent, the hook overrides `{$quote_link}` with a new link that contains an hash that ensures the authenticity of the request. This way only the recipient can accept the quote.

When the visitor clicks the link, the quote is automatically accepted and he/she sees the following modal on screen.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/quote-accepted.png)

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
        <th>{$LANG.manage}</th>
        <th class="responsive-edit-button" style="display: none;"></th>
    </tr>
</thead>
```

Your `thead` could be slightly different (eg. your first column could be the SSL icon check) so change things accordingly. Next move to `tbody` and add the cell right inside `{foreach}` loop.

```
<td class="text-center">
	{if $kt_autologin[$service.id]}
	<div class="btn-group btn-group-sm plesk-login" style="width:60px;">
		<a href="clientarea.php?action=productdetails&id={$service.id}&autologin=1" target="_blank" class="btn btn-primary btn-xs" alt="Click to Login" title="Click to Login" style="padding: 2px 5px;"><img src="templates/{$template}/img/katamaze_autologin/{$kt_autologin[$service.id]->type}.png" style="height:22px; max-width:39px"> <i class="fa fa-sign-in fa-fw" aria-hidden="true"></i></a>
	</div>
	{/if}
</td>
```

We suggest you to replace *Click to Login* with `$LANG` variable for multi-language support. Now we need to disable sorting for the newly added column. On top of the file you'll find the following statement.

```
{include file="$template/includes/tablelist.tpl" tableName="ServicesList" noSortColumns="4" filterColumn="3"}
```

Focus on `noSortColumns="4"`. *4* means that the 5th column will be not sortable (column count start from zero). Change it accordingly. For example if your template uses the SSL check as 1st column, use `noSortColumns="0, 5"`.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AutoLoginToAnyPanelFromMyServices.php)

## Related Service in Ticket Sidebar

Customers can specify the related service/domain on ticket submission but once the ticket has been sent the information is no longer visible. This hook makes sure that related service is always included in ticket sidebar (if specified).

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-related-service-domain-in-ticket-sidebar.PNG)

[Get the Code »](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/hooks/RelatedServiceInInfoTicketSidebar.php)

## Force Payment Gateway depending on Invoice Balance

It doesn't matter what payment method you use. It can be PayPal, Stripe, Skrill or Credit Card. The typical gateway charges absurdly high fees to manage your money. [Billing Extension](https://katamaze.com/whmcs/billing-extension/specifications) helps you [saving up to 18% on transaction fees](https://katamaze.com/docs/billing-extension/4/reducing-the-number-of-invoices#OnePayment) but such costs can be lowered even further.

Let's face it. In an ideal world we would be receiving money just with Bank Transfer (aka Wire Transfer) since it doesn't cost you anything. The following hook can be used to force the most convenient gateway you have depending on invoice balance. For example *if invoice balance >= 1000 euro force banktransfer*. Let's do some math.

* PayPal charges 3.4% + 0.35 € per transaction meaning that receiving 1000 € costs you 35.35 €
* Let's suppose on a yearly basis you receive 10 payments of 1000 €
* At the end of the year you gave to PayPal 353.5 €

With this hook you can keep this money for you. As if it wasn't enough, the hook can be customized to force the payment gateway depending on customers' country. For example you can use the hook just for specific countries (eg. IT, FR, DE) and/or European Union. Don't worry about multiple currencies. The script automatically handles currency conversion when needed.

[Get the Code »](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/hooks/ForcePaymentGatewayDependingOnInvoiceBalance.php)

## Auto-Terminate Free Trials After X Minutes

Free trials for a limited period is a good marketing strategy to capitalize on the leads you get. The problem with trials is that the smallest unit of time for WHMCS is the day meaning that for example you can't provide a trial for VPS that last for a couple of hours. WHMCS can't "think" for a period of less than a full day.

The following action hook allows to automatically terminate the given products/services after a certain number of minutes. It runs AfterCronJob hook point that normally triggers once every 5 minutes. Visit Setup > Automation Settings and make sure that cron.php runs every 5 minuts as suggested by WHMCS. The hook will do the rest. It also logs terminations in Activity Log.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AutoTerminateFreeTrialsAfterXMinutes.php)

## Stronger Password Generator for Auto-Provisioning

We give you not one, not two but three action hooks to override default passwords generated by WHMCS for service provisioning on third-party control panels like Plesk, cPanel, DirectAdmin and custom-made server modules.

* [v1](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/StrongerPasswordGeneratorForAutoProvisioning_v1.php) randomly picks 10 characters from `a-zA-Z0-9` and `!@#$%^&*()-=+?`
* [v2](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/StrongerPasswordGeneratorForAutoProvisioning_v2.php) same as above but makes sure that at least one special character is included in the password
* [v3](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/StrongerPasswordGeneratorForAutoProvisioning_v3.php) for extremely strong passwords. Individually define the number of digits, lowercase, uppercase and special characters to use. The resulting password will not use the same character twice

## One-off Products/Services & Domain purchase require Product/Service

If you have a bit of experience with WHMCS, you know that offering promotions just via [coupon codes](https://docs.whmcs.com/Promotions) isn't so flexible.

Many prefer to have products/services created specifically for special deals. Similarly others want to restrict domain purchase to customers with at least a product/service in their accounts. The hook lets you achieve both goals. Simply configure the following variables:

* `$onetimeProducts` array of product IDs to treat as "one-off" (customer is not allowed to order the same product multiple times)
* `$onetimeProductGroups` same as above but for product group IDs. Producs inside such groups are treated as one-off
* `$firstTimerTollerance` product-based restrictions are disabled for new customers placing their first order with you
* `$notRepeatable` if a customer already has a one-off product, he can't purchase further one-offs (`$firstTimerTollerance` is ignored)
* `$domainRequiresProduct` domain purchase is allowed only if any of the following conditions is met:
	* Customer has an existing product/service (`Pending` and `Terminated` don't count)
	* Customer is purchasing a domain and a product/service
* `$promptRemoval` notify customer about restrictions via (previews are below):
	* `bootstrap-alert` right below Review & Checkout
	* `modal` on screen
	* `js-alert` on scren
* `$textDisallowed` message displayed for product-based restriction
* `$textRequireProduct` message displayed for domain-based resrticion
	
When the hook detects that the customer is not allowed to order specific products/services and/or domains, it removes them from WHMCS cart showing an alert.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-domain-require-product-one-off-products-2.png)

[Get the Code »](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/hooks/OneOffProductsDomainRequireProduct.php)

## New Clients as Affiliates

Automatically sets newly registered customers as Affiliates on WHMCS. This way they don't need to join manually.

That said, as you probably already know the affiliate system of WHMCS is very basic. If you need something more complete and sophisticated take a look at [Commission Manager](https://katamaze.com/whmcs/commission-manager/specifications).

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/NewClientsAsAffiliates.php)

## Send Email & Add Reply on Ticket Status Change

When the status of a support ticket changes, WHMCS doesn't send any notification. We can tweak this process by sending an email and optionally also automatically add a reply to the ticket itself. This way you can guide customers through the resolving process letting them track the progress of tickets.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/SendEmailAndAddReplyOnTicketStatusChange.php)

## Client to Group based on Purchased Product/Service

Automatically assign a customer to a Client Group based on the product/service he/she has just purchased. The script triggers as soon as the order is accepted both manually and automatically.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AssignClientToGroupBasedOnPurchasedProduct.php)

## Prevent changes to Client Custom Fields

WHMCS has an in-built function to lock client profile fields you want to prevent clients being able to edit from clientarea (eg. email, company name). This feature however is not avaiable for client custom fields. Making such fields "disabled" via HTML is not an option. Anyone with bit of knowledge can skip this form of protection.

This hook acts as the last line of defense. It grants that no customer can submit changes. If necessary it can be enabled also for WHMCS Administrators.

If you need something more professional, [Billing Extension](https://katamaze.com/whmcs/billing-extension) can bring your WHMCS to the next level with things like [monthly invoicing](https://katamaze.com/docs/billing-extension/4/reducing-the-number-of-invoices), electronic invoicing, [customer retention](https://katamaze.com/docs/billing-extension/39/client-area#Customer-Retention), [Facebook Pixel](https://katamaze.com/docs/billing-extension/43/facebook-pixel) and much more.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/PreventChangesToClientCustomFields.php)

## Quote to Invoice conversion without redirect

If you are sending out a lot of quotes on a daily basis, the fact that WHMCS forces a redirect to the newly issued invoice could be frustrating. This hook prevents WHMCS from performing the redirect allowing you to keep woriking on the quote.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/QuoteToInvoiceNoRedirect.php)

## Remove/Hide Breadcrumb

WHMCS prepends *Portal Home* to breadcrumb. There's nothing wrong with that but some people don't like it. This hook removes it from all WHMCS pages.

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

## Abort Auto-Provisioning when there's a Note in the Order

A customer orders a VPS and adds notes to request a particular configuration that requires your manual intervention. In case you're using auto-provisioning, there's no way to stop WHMCS from creating the VPS to let you intervene manually. This hook however can stop auto-provisioning when there's a note in the order.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/NoteInTheOrderAbortAutoProvisioning.php)

## Add Button next to Module's Functions

Here is how you can add a button next to *Create*, *Suspend*, *Unsuspend* (...) functions in product/service view.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AddButtonNextToModulesFunctions.php)

## Announcements Meta Description

Before you think *«Great! I can finally add meta descriptions to WHMCS announcements»* wait for a sec and understand the following:

* [WHMCS is terrible at SEO](https://katamaze.com/blog/37/whmcs-seo-ways-to-improve-your-site-ranking-in-2020). You need more than an hook to improve rankings
* [Meta Description](https://katamaze.com/blog/37/whmcs-seo-ways-to-improve-your-site-ranking-in-2020#Meta-description) is **not** a ranking factor. It doesn't affect your rankings but CTR

You can use the same approach to implement other meta tags but stay away from [meta keywords](https://katamaze.com/blog/37/whmcs-seo-ways-to-improve-your-site-ranking-in-2020#Meta-keywords). It is useless and has been deprecated more than a decade ago by all search engines.

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/AnnuncementsMetaDescription.php)

## Promotion Code in Email Template

*Invoice Payment Confirmation* is an email template that WHMCS sends to customers when they pay invoices. By default this message doesn't include any information about promotions. The following hook add coupon code to the invoice recepit (if a promo has been applied).

Once the hook has been added to WHMCS, you can edit *Invoice Payment Confirmation* email template to customize the look of your message like follows.

```
{if $assigned_promos}
Promo below:
{foreach from=$assigned_promos item=promo}
{$promo}
{/foreach}
{/if}
```

Here is a preview of the message.

![image](https://katamaze.com/modules/addons/Mercury/uploads/files/Blog/92b1487d05bc7249c65af0f94cde4732/whmcs-promotion-code-invoice-payment-confirmation.png)

[Get the Code »](https://github.com/Katamaze/WHMCS-Action-Hooks/blob/master/hooks/CouponCodeInEmailTemplate.php)

## Automatically Accept Order when Invoice is Paid

WHMCS requires administrators to manually accept orders even if automation tasks already took place. This hook automatically accepts orders via API when Invoice is paid.

[Get the Code »](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/hooks/AcceptOrderOnInvociePaid.php)

## Hide Google Invisible reCAPTCHA Badge

All it takes to hide Google Invisible reCAPTCHA Badge (bottom-right corner) is a CSS rule. If you don't want to edit your CSS and/or want preserve the chgange with template updates, use this hook. Before you ask, yes, the correct way to hide the Badge is to use `opacity`. Using things like `display: none` and `visibility: hidden` breaks reCAPTCHA.

[Get the Code »](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/hooks/HideGoogleInvisibleReCAPTCHA.php)

## Chatstack Disable for Logged-In Users and Administrators

In case you have now idea of what Chatstack is, let me give you a little bit of background. It's an official module for WHMCS that allows to track and chat with visitors. We use it ourselves on [our site](https://katamaze.com/). It's the little badge at the bottom right corner. Visitors can click it to start chatting with us. In case we're not online, the badge redirects to *contact us*.

It is worth to mention that in past Chatstack was named LiveHelp. You can purchase it directly from [chatstack.com](https://www.chatstack.com/) or from WHMCS [Marketplace](https://marketplace.whmcs.com/product/34-live-chat-visitor-tracking). Ignore all the negative reviews. Most of them are from people that have no idea of how to install and configure it 😑

Let's now move to the hook itself. Once Chatstack is installed on your WHMCS site, it starts tracking everyone including WHMCS administrators and logged-in users. This creates the following problems:

* You receive notifications about administrators' activities
* Chatstack puts visitors and administrators on the same level
* Most companies want to use the chat for pre-sales and not for support requests

The hook we made provides two options that allows to:

* Stop tracking and notifying administrators' activities
* Prevent logged-in users (existing customers) to use the chat

The only requirement is that you remove any existing integration between WHMCS & Chatstack. The action hook handles everything and supports also [WHMCS multi-domain and multi-brand](https://katamaze.it/docs/mercury/48/multi-brand-e-geolocalizzazione#Multi-brand-e-multi-dominio).

[Get the Code »](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/hooks/ChatstackDisableLoggedInAndAdmin.php)

## Notify Fradulent Orders

When an order is set as fraud, prior to the change of status actually occurring, the hook sends email notifications to all existing WHMCS administrators (disabled administrators are ignored).

[Get the Code »](https://github.com/Katamaze/WHMCS-Free-Action-Hooks/blob/master/hooks/NotifyFradulentOrders.php)
