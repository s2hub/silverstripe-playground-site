<?php

use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Versioned\Versioned;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Creates the sample content of this site: pages built from content blocks, and contacts in categories.
 *
 * Run it once after the first dev/build: vendor/bin/sake tasks:seed-content
 * It does nothing when there are contacts already.
 */
class SeedContentTask extends BuildTask
{
    protected static string $commandName = 'seed-content';

    protected string $title = 'Seed content';

    protected static string $description = 'Create the sample pages, content blocks and contacts (once)';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        // Pages and blocks are written as drafts, then published (sake runs in the live stage).
        Versioned::set_stage(Versioned::DRAFT);

        if (Contact::get()->exists()) {
            $output->writeln('There are contacts already: nothing to do.');

            return Command::SUCCESS;
        }

        $siteConfig = SiteConfig::current_site_config();
        $siteConfig->Title = 'Silverstripe Playground Site';
        $siteConfig->Tagline = 'A small site to learn Silverstripe CMS from';
        $siteConfig->write();

        $categories = $this->createContacts();
        $output->writeln(sprintf('Created %d contacts in %d categories', Contact::get()->count(), count($categories)));

        // The default pages of the first dev/build make way for the sample pages.
        foreach (['about-us', 'contact-us'] as $link) {
            if ($page = SiteTree::get_by_link($link)) {
                $page->doUnpublish();
                $page->delete();
            }
        }

        $this->createPage('home', 'Home', 1, [
            $this->text('Welcome', <<<'HTML'
                <p class="intro">This is a small Silverstripe CMS site to learn from. Every page is built from
                content blocks, and the contacts below the menu item <em>Contacts</em> come from a ModelAdmin: the
                two things most Silverstripe sites are made of.</p>
                <p>Log in to the CMS at <a href="admin/">/admin</a>, change some content, then look at the code that
                makes it work: everything specific to this site is in the folder <code>app/</code>.</p>
                HTML),
            $this->text('What to look at', <<<'HTML'
                <ul>
                    <li><a href="how-it-is-built/">How it is built</a>: the pieces of this site and where they are.</li>
                    <li><a href="contacts/">Contacts</a>: a content block that lists records managed in the CMS.</li>
                    <li><a href="try-it/">Try it</a>: small tasks to extend the site.</li>
                </ul>
                HTML),
        ]);

        $this->createPage('how-it-is-built', 'How it is built', 2, [
            $this->text('Pages and content blocks', <<<'HTML'
                <p>The pages are of the type <code>Page</code> (<code>app/src/Page.php</code>). The module
                <a href="https://github.com/silverstripe/silverstripe-elemental">silverstripe-elemental</a> turns their
                content into a list of blocks (<code>app/_config/elemental.yml</code>): in the CMS, a page shows its
                blocks instead of a single text field.</p>
                <p>This text is a block of the type <em>Content</em>, which comes with the module. The theme is the
                standard <em>Startup</em> theme of Silverstripe.</p>
                HTML),
            $this->text('Contacts: a ModelAdmin', <<<'HTML'
                <p>Records that are not pages are DataObjects. The contacts are two of them:
                <code>Contact</code> and <code>ContactCategory</code> (<code>app/src/Contacts/</code>). A ModelAdmin,
                <code>ContactAdmin</code>, gives them the section <em>Contacts</em> in the CMS, with a list, filters
                and an edit form. Database fields are declared in <code>$db</code>, relations in
                <code>$has_one</code> and <code>$has_many</code>; <code>dev/build</code> creates the tables.</p>
                HTML),
            $this->text('A block of our own', <<<'HTML'
                <p><code>ContactsBlock</code> (<code>app/src/Blocks/</code>) is a content block that lists contacts,
                all of them or those of one category. Its template is <code>app/templates/ContactsBlock.ss</code>.
                Each contact has a page of its own (<code>ContactController</code>, routed in
                <code>app/_config/routes.yml</code>, e.g. <code>/contact/1</code>), which the CMS also shows as the
                preview of a contact.</p>
                HTML),
        ]);

        $this->createPage('contacts', 'Contacts', 3, [
            $this->text('Our contacts', <<<'HTML'
                <p>The list below is a <em>Contacts</em> block. Edit the contacts in the CMS under
                <em>Contacts</em>; choose a category in the block to show only part of them.</p>
                HTML),
            ContactsBlock::create(['Title' => 'All contacts', 'ShowTitle' => false]),
            ContactsBlock::create(['Title' => 'Press', 'ShowTitle' => true, 'CategoryID' => $categories['Press']->ID]),
        ]);

        $this->createPage('try-it', 'Try it', 4, [
            $this->text('Small tasks', <<<'HTML'
                <ol>
                    <li>Add a <code>Birthday</code> field to <code>Contact</code> (<code>'Birthday' =&gt; 'Date'</code>),
                    run <code>dev/build</code> and show it in the list in the CMS (<code>$summary_fields</code>).</li>
                    <li>Add a <code>Favourite</code> checkbox (<code>'Favourite' =&gt; 'Boolean'</code>) and make it a
                    filter of the list (<code>$searchable_fields</code>).</li>
                    <li>Show the phone number in the Contacts block (<code>app/templates/ContactsBlock.ss</code>).</li>
                    <li>Give contacts tags: a <code>ContactTag</code> with a <code>many_many</code> relation, managed in
                    a third tab of <code>ContactAdmin</code>.</li>
                    <li>Write a block of your own, e.g. a quote with an author, and add it to this page.</li>
                </ol>
                HTML),
        ]);

        $output->writeln('Created the pages Home, How it is built, Contacts and Try it');

        return Command::SUCCESS;
    }

    /**
     * @return array<string, ContactCategory> The categories by title
     */
    private function createContacts(): array
    {
        $contacts = [
            'Customers' => [
                ['Ada', 'Lovelace', 'Analytical Engines Ltd', 'ada@example.com', '+44 20 7946 0001'],
                ['Grace', 'Hopper', 'Compiler Works', 'grace@example.com', '+1 202 555 0102'],
                ['Alan', 'Turing', 'Bletchley Systems', 'alan@example.com', '+44 20 7946 0003'],
            ],
            'Partners' => [
                ['Margaret', 'Hamilton', 'Apollo Software', 'margaret@example.com', '+1 617 555 0104'],
                ['Tim', 'Berners-Lee', 'Web Foundation', 'tim@example.com', '+44 20 7946 0005'],
            ],
            'Press' => [
                ['Katherine', 'Johnson', 'Orbit Weekly', 'katherine@example.com', '+1 757 555 0106'],
                ['Linus', 'Torvalds', 'Kernel News', 'linus@example.com', '+358 9 555 0107'],
            ],
        ];
        $categories = [];

        foreach ($contacts as $title => $people) {
            $categories[$title] = ContactCategory::create(['Title' => $title]);
            $categories[$title]->write();

            foreach ($people as [$firstName, $surname, $company, $email, $phone]) {
                Contact::create([
                    'FirstName' => $firstName,
                    'Surname' => $surname,
                    'Company' => $company,
                    'Email' => $email,
                    'Phone' => $phone,
                    'CategoryID' => $categories[$title]->ID,
                ])->write();
            }
        }

        return $categories;
    }

    private function text(string $title, string $html): ElementContent
    {
        return ElementContent::create(['Title' => $title, 'ShowTitle' => true, 'HTML' => $html]);
    }

    /**
     * Creates or updates a page with the given blocks, and publishes it with them.
     */
    private function createPage(string $urlSegment, string $title, int $sort, array $blocks): Page
    {
        $page = SiteTree::get_by_link($urlSegment) ?: Page::create();
        $page->update([
            'Title' => $title,
            'URLSegment' => $urlSegment,
            'Sort' => $sort,
            'Content' => '',
        ]);
        $page->write();

        foreach ($blocks as $index => $block) {
            $block->ParentID = $page->ElementalArea()->ID;
            $block->Sort = $index + 1;
            $block->write();
        }

        $page->publishRecursive();

        return $page;
    }
}
