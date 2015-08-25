<div class="{$Classes}">
<div class="carousel-container">
    <div class="carousel<% if $SliderItems.Count > 1 %> carousel--multiple<% else %> carousel--single<% end_if %> owl-carousel ">
        <% loop $SliderItems %>
            <div class="carousel__item is-{$EvenOdd}<% if $FirstLast %> is-{$FirstLast}<% end_if %>">
                {$Image.croppedImage(1140, 641)}
            </div><!-- /.carousel__item -->
        <% end_loop %>
    </div><!-- /.carousel owl-carousel -->
    <% if $SliderItems.Count > 1 %>
        <div class="carousel-navigation">
            <div class="carousel-navigation__item carousel-navigation__item--prev">{$SVG('chevron-left').extraClass('carousel-navigation__item__icon')}</div><!-- /.carousel-navigation__item -->
            <div class="carousel-navigation__item carousel-navigation__item--next next">{$SVG('chevron-right').extraClass('carousel-navigation__item__icon')}</div><!-- /.carousel-navigation__item -->
        </div><!-- /.carousel-navigation -->
    <% end_if %>
</div><!-- /.carousel-container -->
</div><!-- /.{$Classes} -->