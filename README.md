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

