;
(function ($) {
  /** -----------------------------------------
   * Actions
   -------------------------------------------*/
  var $wrapper = $('.sitebuilder'),
    $loadingClass = 'is-loading',
    $container = $('.site-builder'),
    $select = $('.js-select'),
    $debug = false; // Enable console logs.

  /**
   * @param $key
   * @param $value
   */
  function debug($key, $value) {
    if ($debug) {
      console.log($key, $value);
    }
  }

  $select.entwine({
    onchange: function (e) {
      e.preventDefault();
      var $wrapper = $('.sitebuilder'),
        $container = $('.site-builder'), // Set new instances, since these gets re-built
        $self = this,
        $action = $('option:selected', $self).data('action');
      $.ajax({
        url: $action,
        beforeSend: function (xhr) {
          debug('- - - - - - - - - - - - - - - - - - - - -', 'action');
          $wrapper.loading(true);
        }
      })
        .done(function (response) {
          $container.redraw();
          /**
           * Prevent the prompt to save changes, since some of the actions are form items.
           */
          window.onbeforeunload = null;
        })
        .fail(function (xhr) {
          console.log('Error: ' + xhr.responseText);
        });
      return false;
    }
  });
  $container.entwine({
    onmatch: function () {
      var $self = this;
      $self.initialize();
      this._super();
    },
    onunmatch: function () {
      this._super();
    },
    /** -----------------------------------------
     * Initialize
     * ----------------------------------------*/
    initialize: function () {
      debug('Site Builder', this);
      var $self = this,
        $placeholderHeight = 0,
        $containerContent = $('.site-builder__container__content', $self);
      /**
       * Add jQuery Sortable to Containers
       */
      this.sortable({
        placeholder: 'site-builder__placeholder',
        items: '.site-builder__container',
        create: function () {
          debug('Container Sortable', 'Initialised');
        },
        sort: function (event, ui) {
          if ($placeholderHeight <= 0) {
            $('.site-builder__placeholder').css({
              height: ui.item.innerHeight()
            });
          }
        },
        stop: function () {
          $placeholderHeight = 0;
          var $sorted = $self.sortable('toArray');
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
        create: function () {
          debug('Item Sortable', 'Initialised');
        },
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
    },
    /** -----------------------------------------
     * Sort Items
     * ----------------------------------------*/
    sort: function ($items, $className) {
      var $wrapper = $('.sitebuilder'); // Set a new instance, since this gets re-built
      debug('- - - - - - - - - - - - - - - - - - - - -', 'sorting');
      debug('Sorted Items:', $items);
      var $self = this;
      $.ajax({
        type: 'POST',
        url: $self.data('url') + '/siteBuilderSort',
        data: {
          items: $items,
          className: $className
        },
        beforeSend: function (xhr) {
          debug('Sorting', 'beforeSend');
          $wrapper.loading(true);
        }
      })
        .done(function (response) {
          debug('Sorting', 'done');
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
      var $wrapper = $('.sitebuilder'), // Set a new instance, since this gets re-built
        $self = this;
      $.ajax({
        url: $self.data('url') + '/siteBuilderReDraw'
      })
        .done(function (response) {
          $wrapper.redraw(response);
          $self.initialize();
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
      var $wrapper = $('.sitebuilder'),
        $container = $('.site-builder'); // Set new instances, since these gets re-built
      /** If there's already an action happening. */
      if ($wrapper.isLoading()) {
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
    onmatch: function () {
      debug('Wrapper', this);
      this._super();
    },
    isLoading: function () {
      $self = this;
      return $self.hasClass($loadingClass);
    },
    loading: function ($state) {
      var $self = this;
      if ($state) {
        debug('Loading', 'Initialised');
        $self.addClass($loadingClass);
      } else {
        debug('Loading', 'Completed');
        $self.removeClass($loadingClass);
      }
    },
    redraw: function ($response) {
      debug('- - - - - - - - - - - - - - - - - - - - -', 'wrapper redraw');
      var $self = this;
      $self.loading(false);
      $self.closest('fieldset').html($response);
    }
  });
})(jQuery);