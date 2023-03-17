<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Behat\Context;

use Sweetchuck\DrupalTestTraits\Core\Behat\Context\Base;

class Browser extends Base {

  /**
   * @Given the :selector element is in the view
   * @When I scroll to :selector element into view
   *
   * @param string $selector
   *   CSS or XPath selector.
   *   Examples:
   *   - .my-class
   *   - div
   *   - #my-id
   *   - css:.my-class
   *   - css:div
   *   - css:#my-id
   *   - xpath://p/strong
   *   CSS is the default locator type.
   */
  public function scrollToElementBySelector(string $selector): void {
    // @todo Check that the element is exists.
    $locator = 'css';
    $matches = [];
    if (preg_match('/^(?P<locator>jquery|css|xpath):/', $selector, $matches) === 1) {
      $locator = $matches['locator'];
      $selector = preg_replace('/^(jquery|css|xpath):/', '', $selector);
    }

    $selectorSafe = var_export($selector, TRUE);

    switch ($locator) {
      case 'jquery':
        $script = <<<JS
(function() {
  jQuery($selectorSafe)[0]
    .scrollIntoView(true);
})()
JS;
        break;

      case 'xpath':
        $script = <<<JS
(function() {
  document
    .evaluate($selectorSafe, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null)
    .singleNodeValue
    .scrollIntoView(true);
})()
JS;
        break;

      default:
        $script = <<<JS
(function() {
  document
    .querySelector($selectorSafe)
    .scrollIntoView(true);
})()
JS;
        break;
    }

    $this->getSession()->executeScript($script);
  }

}
