(function (Drupal) {

  Drupal.behaviors.appbModuleFilterTabs = {
    attach: function () {
      Drupal.ModuleFilter.tabs.setActive = function (tab) {
        if (this.activeTab) {
          this.activeTab.hideSummary();
        }

        this.activeTab.element.parent()
          .find('> .is-next, > .is-selected, > .is-previous')
          .removeClass(['is-next', 'is-selected', 'is-previous']);

        this.activeTab = tab;
        this.activeTab.element.addClass('is-selected');

        this.activeTab.element.next().addClass('is-next');
        this.activeTab.element.prev().addClass('is-previous');

        this.activeTab.showSummary();

        return this.activeTab;
      };

      Drupal.ModuleFilter.tabs.setActive(Drupal.ModuleFilter.tabs.getActive());
    },
  };

})(Drupal);
