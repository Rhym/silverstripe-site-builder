<% if $PageBuilderContainers %>
    <% loop $PageBuilderContainers %>
        <div class="site-builder__item"{$InlineStyle}>
            <% if $Children %>
                <% if $IsFullWidth == 0 %><div class="container"><% end_if %>
                <div class="row">
                    <% loop $Children %>
                        {$forTemplate}
                    <% end_loop %>
                </div><!-- /.row -->
                <% if $IsFullWidth == 0 %></div><!-- /.container --><% end_if %>
            <% end_if %>
        </div><!-- /.site-builder__item -->
    <% end_loop %>
<% end_if %>