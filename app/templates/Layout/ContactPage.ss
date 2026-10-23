<% require css('app/client/css/contacts.css') %>
<div class="page__content">
    <% with $Contact %>
        <h1 class="page__title">$Title</h1>
        <dl class="contact">
            <% if $Company %><dt>Company</dt><dd>$Company</dd><% end_if %>
            <% if $Category %><dt>Category</dt><dd>$Category.Title</dd><% end_if %>
            <% if $Email %><dt>Email</dt><dd><a href="mailto:$Email">$Email</a></dd><% end_if %>
            <% if $Phone %><dt>Phone</dt><dd><a href="tel:$Phone">$Phone</a></dd><% end_if %>
        </dl>
    <% end_with %>
</div>
