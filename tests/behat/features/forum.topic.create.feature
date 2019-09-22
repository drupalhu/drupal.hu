Feature: Forum topic create

  Background:
    Given users:
      | name    | mail                    |
      | dummy-1 | dummy-1@behat.localhost |
    And I am logged in as "dummy-1"

  @api
  Scenario: Forum topic create - navigate
    When I click "Fórum" in the "Fő navigáció" menu
    And I click "Új Fórumtéma hozzáadása"
    Then I should see an "form[data-drupal-selector='node-forum-form']" element

  @api
  Scenario: Forum topic create - required fields
    Given I am on "/node/add/forum"
    When I press "Mentés"
    Then I should see only the following error messages:
      | Fórumok mező szükséges. |
      | Tárgy mező szükséges.   |

  @api @javascript
  Scenario: Forum topic create - success
    Given I am on "/node/add/forum"
    When I select "Modulok fejlesztése" from "Fórumok"
    And I fill in "Tárgy" with "My topic 01"
    And I fill in wysiwyg on field "app_body[0][value]" with "<p>My body</p>"
    And I select "8.x" from "Drupal verzió"
    And I press "Mentés"
    Then I should see 1 message
    And I should see only the following status message:
      | My topic 01 Fórumtéma létrejött. |
