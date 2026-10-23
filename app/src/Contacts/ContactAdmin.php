<?php

use SilverStripe\Admin\ModelAdmin;

/**
 * The Contacts section of the CMS: one tab for the contacts, one for their categories.
 */
class ContactAdmin extends ModelAdmin
{
    private static $managed_models = [
        Contact::class,
        ContactCategory::class,
    ];

    private static $url_segment = 'contacts';

    private static $menu_title = 'Contacts';

    private static $menu_icon_class = 'font-icon-torsos-all';
}
