<% require css('app/client/css/contacts.css') %>
<% if $ShowTitle %>
    <h2>$Title</h2>
<% end_if %>
<ul class="contacts">
    <% loop $Contacts %>
        <li class="contacts__item">
            <a class="contacts__name" href="$Link">$Title</a>
            <% if $Company %><span class="contacts__company">$Company</span><% end_if %>
            <% if $Email %><a class="contacts__email" href="mailto:$Email">$Email</a><% end_if %>
        </li>
    <% end_loop %>
</ul>
