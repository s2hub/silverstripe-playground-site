<?php

use DNADesign\Elemental\Models\BaseElement;

/**
 * A content block that lists contacts: all of them, or those of one category. Its template is
 * templates/ContactsBlock.ss.
 */
class ContactsBlock extends BaseElement
{
    private static $singular_name = 'Contacts';

    private static $plural_name = 'Contacts blocks';

    private static $description = 'A list of contacts';

    private static $icon = 'font-icon-torsos-all';

    private static $has_one = [
        'Category' => ContactCategory::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->dataFieldByName('CategoryID')
            ->setTitle('Category')
            ->setEmptyString('All contacts');

        return $fields;
    }

    public function getType()
    {
        return 'Contacts';
    }

    /**
     * The contacts to show.
     */
    public function getContacts()
    {
        return $this->CategoryID ? $this->Category()->Contacts() : Contact::get();
    }

    /**
     * The summary in the list of blocks in the CMS.
     */
    public function getSummary()
    {
        return $this->CategoryID ? $this->Category()->Title : 'All contacts';
    }
}
