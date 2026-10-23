<?php

use SilverStripe\ORM\DataObject;

/**
 * A group of contacts, e.g. customers or press. Managed in the CMS under Contacts > Categories.
 */
class ContactCategory extends DataObject
{
    private static $db = [
        'Title' => 'Varchar',
    ];

    private static $has_many = [
        'Contacts' => Contact::class,
    ];

    private static $summary_fields = [
        'Title' => 'Category',
        'Contacts.Count' => 'Contacts',
    ];

    private static $default_sort = 'Title';
}
