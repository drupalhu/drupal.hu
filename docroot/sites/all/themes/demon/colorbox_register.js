(function ($) {

Drupal.behaviors.initColorboxRegister = {
  attach: function (context, settings) {
    if (!$.isFunction($.colorbox)) {
      return;
    }

    $("a[href*='/user/register'], a[href*='?q=user/register']", context)
      .once('init-colorbox-login-processed', function () {
        var path = this.href;
        var new_path = path.replace(/user\/register/, 'colorbox/form/user_register_form');
        var addquery = (path.indexOf('?') != -1) ? '&' : '?';

        // If no destination, add one to the current page.
        if (path.indexOf('destination') != -1) {
          this.href = new_path;
        }
        else {
          this.href = new_path + addquery + 'destination=' + window.location.pathname.substr(1);
        }
      })
      .colorbox();
  }
};

})(jQuery);
