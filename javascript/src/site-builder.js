;
(function ($) {
  $.entwine('cms', function ($) {
    /** -----------------------------------------
     * Actions
     -------------------------------------------*/
    var $wrapper = $('.sitebuilder'),
      $loadingClass = 'is-loading',
      $container = $('.site-builder');
    $('.js-select').entwine({
      onchange: function (e) {
        $self = this;
        /** If there's already an action happening. */
        if ($wrapper.hasClass($loadingClass)) {
          return false;
        }
        var $action = $('option:selected', $self).data('action');
        $.ajax({
          url: $action,
          beforeSend: function (xhr) {
            $wrapper.loading(true);
          }
        })
          .done(function (response) {
            $container.redraw();
          })
          .fail(function (xhr) {
            console.log('Error: ' + xhr.responseText);
          });
      }
    });
    $container.entwine({
      onmatch: function () {
        var $self = this,
          $placeholderHeight = 0,
          $containerContent = $('.site-builder__container__content', $self);
        /**
         * Add jQuery Sortable to Containers
         */
        this.sortable({
          placeholder: 'site-builder__placeholder',
          items: '.site-builder__container',
          sort: function (event, ui) {
            if ($placeholderHeight <= 0) {
              $('.site-builder__placeholder').css({height: ui.item.innerHeight()});
            }
          },
          stop: function () {
            $placeholderHeight = 0;
            var $sorted = $container.sortable('toArray');
            $self.sort($sorted, 'PageBuilderContainer');
          }
        });
        this.disableSelection();
        /**
         * Add jQuery Sortable to Container Children
         */
        $containerContent.sortable({
          items: '.site-builder__container__content__item',
          placeholder: 'site-builder__container__content__placeholder',
          sort: function (event, ui) {
            if ($placeholderHeight <= 0) {
              var $child = $('.site-builder__container__content__item__content', ui.item);
              $('.site-builder__container__content__placeholder').css({
                height: ($child.innerHeight()),
                width: (ui.item.innerWidth() - 2)
              });
            }
          },
          stop: function (event, ui) {
            $placeholderHeight = 0;
            var $sorted = ui.item.closest('.site-builder__container__content').sortable('toArray');
            $self.sort($sorted, 'PageBuilderItem');
          }
        });
        $containerContent.disableSelection();
        this._super();
      },
      onunmatch: function () {
        this._super();
      },
      /** -----------------------------------------
       * Sort Items
       * ----------------------------------------*/
      sort: function ($items, $className) {
        var $self = this;
        $.ajax({
          type: 'POST',
          url: $self.data('url') + '/siteBuilderSort',
          data: {
            items: $items,
            className: $className
          },
          beforeSend: function (xhr) {
            $wrapper.loading(true);
          }
        })
          .done(function (response) {
            $self.redraw();
          })
          .fail(function (xhr) {
            console.log('Error: ' + xhr.responseText);
          });
      },
      /** -----------------------------------------
       * Reload Wrapper
       * ----------------------------------------*/
      redraw: function () {
        var $self = this;
        $.ajax({
          url: $self.data('url') + '/siteBuilderReDraw'
        })
          .done(function (response) {
            $wrapper.loading(false);
            $wrapper.html(response);
          })
          .fail(function (xhr) {
            console.log('Error: ' + xhr.responseText);
          });
      }
    });

    /** -----------------------------------------
     * Actions
     * ----------------------------------------*/
    $('.js-action').entwine({
      onclick: function (e) {
        e.preventDefault();
        /** If there's already an action happening. */
        if ($wrapper.hasClass($loadingClass)) {
          return false;
        }
        var $actionButton = $(this);
        /**
         * Initiate a confirmation check.
         */
        if ($actionButton.data('confirm') == true) {
          var $confirm = confirm('Are you sure you want to remove this item?');
          if ($confirm != true) {
            return false;
          }
        }
        $.ajax({
          url: this.attr('href'),
          beforeSend: function (xhr) {
            $wrapper.loading(true);
            $actionButton.addClass($loadingClass);
          }
        })
          .done(function (response) {
            $container.redraw();
          })
          .fail(function (xhr) {
            console.log('Error: ' + xhr.responseText);
          });
      }
    });
    $wrapper.entwine({
      loading: function ($state) {
        if ($state) {
          $wrapper.addClass($loadingClass);
        } else {
          $wrapper.removeClass($loadingClass);
        }
      }
    });
  });
})(jQuery);