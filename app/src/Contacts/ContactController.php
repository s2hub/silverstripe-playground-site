<?php

use SilverStripe\Control\HTTPRequest;

/**
 * The page of a contact: /contact/<ID>, in the site's layout (templates/Layout/ContactPage.ss).
 */
class ContactController extends PageController
{
    public function index(HTTPRequest $request)
    {
        $contact = Contact::get()->byID((int) $request->param('ID'));

        if (!$contact) {
            return $this->httpError(404);
        }

        return $this->customise([
            'Title' => $contact->getTitle(),
            'Contact' => $contact,
        ])->renderWith(['ContactPage', 'Page']);
    }
}
