<?php

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;

/**
 * A person in the address book, with a category. Managed in the CMS under Contacts (see ContactAdmin), shown on the
 * site by the Contacts block (see ContactsBlock) and on a page of its own (see ContactController).
 *
 * As a CMSPreviewable, its edit form in the CMS shows that page as a preview.
 */
class Contact extends DataObject implements CMSPreviewable
{
    private static $db = [
        'FirstName' => 'Varchar',
        'Surname' => 'Varchar',
        'Email' => 'Varchar(254)',
        'Phone' => 'Varchar(50)',
        'Company' => 'Varchar',
    ];

    private static $has_one = [
        'Category' => ContactCategory::class,
    ];

    // The columns of the list in the CMS.
    private static $summary_fields = [
        'FirstName' => 'First name',
        'Surname' => 'Surname',
        'Company' => 'Company',
        'Email' => 'Email',
        'Category.Title' => 'Category',
    ];

    // The filters of the list in the CMS.
    private static $searchable_fields = [
        'FirstName',
        'Surname',
        'Company',
        'CategoryID' => [
            'title' => 'Category',
            // Silverstripe 6.2 cannot scaffold this filter for a has_one by itself: the field is given here, and its
            // options are set in scaffoldSearchFields().
            'field' => DropdownField::class,
            'filter' => 'ExactMatchFilter',
        ],
    ];

    private static $default_sort = 'Surname, FirstName';

    // Contacts have no draft and live versions: the CMS shows their preview only when asked to.
    private static $show_unversioned_preview_link = true;

    /**
     * The name shown in breadcrumbs, links and the page title.
     */
    public function getTitle()
    {
        return trim($this->FirstName . ' ' . $this->Surname);
    }

    /**
     * The category filter lists the categories (see searchable_fields).
     */
    public function scaffoldSearchFields($_params = null)
    {
        $fields = parent::scaffoldSearchFields($_params);
        $fields->dataFieldByName('CategoryID')
            ->setSource(ContactCategory::get()->map())
            ->setEmptyString('Any category');

        return $fields;
    }

    /**
     * The contact's page on the site.
     */
    public function Link($action = null)
    {
        return Controller::join_links(Director::baseURL(), 'contact', $this->ID, $action);
    }

    public function PreviewLink($action = null)
    {
        return $this->isInDB() ? $this->Link($action) : null;
    }

    public function getMimeType()
    {
        return 'text/html';
    }

    public function getCMSEditLink(): ?string
    {
        return ContactAdmin::singleton()->getCMSEditLinkForManagedDataObject($this);
    }
}
