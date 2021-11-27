(function ($, Drupal) {

  Drupal.behaviors.appbTableSort = {
    attach: function (context, settings) {
      $('table.appb-table-sort', context)
        .once('appbTableSort')
        .each(function () {
          new TableSort(this);
        });
    },
  };

})(jQuery, Drupal);
