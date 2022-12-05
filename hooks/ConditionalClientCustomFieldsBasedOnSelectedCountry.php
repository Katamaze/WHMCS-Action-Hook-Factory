<?php

/**
 * Conditional Client Custom Fields based on selected country
 *
 * Tested on `register.php` and `cart.php?a=checkout` on WHMCS 8.6 and Six-based
 * templates. Custom templates or twenty-one could require a bit of tuning. Basically
 * just changing HTML selectors. Keep in mind this is just a front-end validation
 * 
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

define('kt_custom_fields_by_country', [ 

    'IT' => [ 1, 2 ], // When `IT` (Italy) is selected only custom fields ID `1` and `2` are displayed. Other fields are automatically hidden
    'IL' => [ 3, 4 ] // When `IL` (Israel) is selected only custom fields ID `3` and `4` are displayed. Other fields are automatically hidden
]);

// Array is empty. Nothing to do. Exiting...
if (!kt_custom_fields_by_country) {

    return;
}

add_hook('ClientAreaHeadOutput', 1, function($vars) {

    // The hook triggers only on `regsiter.php` and `cart.php?a=checkout` where users register on WHMCS
    if ($vars['filename'] != 'register' AND ($vars['filename'] != 'cart' AND $_GET['a'] != 'checkout')) {

        return;
    }

    $output = '

<script type="text/javascript">

// Passing `kt_custom_fields_by_country` to jQuery via JSON object 
var kt_custom_fields_by_country = ' . json_encode(kt_custom_fields_by_country) . ';

// Detecting currently selected country on page load
$(document).on("change", "#inputCountry", function(e) {

    // Calling the function that handles everything
    hideShowCustomFields($("#inputCountry").val());
});

// Detecting selected country when the select changes
$(document).ready(function() {

    // Calling the function that handles everything
    hideShowCustomFields($("#inputCountry").val());
});

// The function that hide/show custom fields based on the currently selected country
hideShowCustomFields = function(country) {

    // The currently selected country has a rule defined in `kt_custom_fields_by_country`
    if (kt_custom_fields_by_country.hasOwnProperty(country)) {

        // For every custom fields in the page...
        $.each($("input[id^=\"customfield\"]"), function(i, item) {

            // I extract the ID number from HTML ID attribute and I parse it as `int`
            var custom_field_id = parseInt($(item).attr("id").replace("customfield", ""));

            // If current custom field ID is found in `kt_custom_fields_by_country[country]` I make it visible...
            if ($.inArray(custom_field_id, kt_custom_fields_by_country[country]) > -1) {

                $("#customfield" + custom_field_id).closest(".form-group").removeClass("hidden");
            }
            // Otherwise I hide it
            else {

                $("#customfield" + custom_field_id).closest(".form-group").addClass("hidden");
            }
        });
    }
    // No rules defined in `kt_custom_fields_by_country` for the currently selected country
    else {

        // Making sure every custom fields is visible since we don\'t need to hide/show them conditionally
        $.each($("input[id^=\"customfield\"]"), function(i, item) {

            // Extracting the ID number from HTML ID attribute and parsing it as `int`
            var custom_field_id = parseInt($(item).attr("id").replace("customfield", ""));

            // Making all custom fields visible
            $("#customfield" + custom_field_id).closest(".form-group").removeClass("hidden");
        });
    }
}

</script>

';

    return $output;

});
