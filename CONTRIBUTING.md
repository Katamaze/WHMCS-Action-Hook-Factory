# Contributing

First off, thanks for taking the time to contribuite 👍

The point of this this package is to help **Developers**, **Hosting Providers**, **Web Agencies** and **IT professionals** to perfect WHMCS. Over the years we kept improving our code based on customers' feedback but together we can make it even better.

Feel free to propose changes to existing scripts, request new action hooks and report bugs.

Please read the following FAQ to know more about coding conventions.

# Frequently Asked Question

## Why do you keep using `ClientAreaPage` when a more specific hook point is available?

For us backward compatibility has always been important since we have customers still running outdated versions of WHMCS (it doesn't depend on us ☹️). That said, we know we can use `ClientAreaPageHome` in place of `ClientAreaPage` to "play" with home page. The problem is that older versions of WHMCS only have `ClientAreaPage`. That's why we keep using it.

## You use `Capsule::raw` a lot. Why?

We dislike Laravel (aka Capsule) for more than one reason but that's another story. Simply put, we value **readability over verbosity**. A `JOIN` between multiple tables is not a big thing but we think that Laravel makes this unnecessarily long and confusing hence we make it `raw`.

## Can I use short open tag `<?`

Nope. Some servers don't support it so we don't use it. Recap:

* Start with `<?php`
* Don't use closing tag `?>`

## Can I place `use` operator wherever I need?

No. All `use` statements must be on top of the file right after `<?php` opening tag. Don't place `use` in the middle of the hook. You don't know where people copy/paste the hook and we don't want to see `Whoops\Exception\ErrorException` (...) `the name is already in use`.


## What about `$LANG`?

We are open to discussion but we avoid implementing multi-language in action hooks since we can't "predict" name collision.

## `return` or nested `if`?

Readability hands down. Do this:

```
if ($country != 'IT'): return; endif;
if ($federation != 'EU'): return; endif;
if ($currency != 'EUR'): return; endif;
if (!$taxed): return; endif;
if (!in_array($clientID, $customers)): return; endif;

die('Done!');
```

Don't do this:

```
if ($country == 'IT')
{
    if ($federation == 'EU')
    {
        if ($currency == 'EUR')
        {
            if ($taxed)
            {
                if (in_array($clientID, $customers))
                {
                    die('Done!');
                }
            }
        }
    }
}
```

## Single quote `'` or double `"` ?

Whenever possible use choose single quotes over double ones.
