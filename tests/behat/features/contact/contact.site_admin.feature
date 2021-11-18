@api
@javascript
Feature: Site wide contact form

  Scenario Outline: Site wide contact form – navigation
    Given I am acting as a user with the "<roles>" role
    And I am on the homepage
    When I click "Kapcsolat" in the "Lábléc" menu block
    Then the url should match "/kapcsolat"
    And I should see an "form[data-drupal-selector='contact-message-site-admin-form']" element
    Examples:
      | roles         |
      | anonymous     |
      | authenticated |
