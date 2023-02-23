@api
#noinspection NonAsciiCharacters
Feature: Forum topic create

  Background:
    Given users:
      | name    | mail                    |
      | dummy-1 | dummy-1@behat.localhost |
    And I am logged in as "dummy-1"

  Scenario: Forum topic create - anonymous
    Given I am an anonymous user
    When I am on "/node/add/forum"
    Then I should get a 403 HTTP response

  @javascript
  Scenario: Forum topic create - navigate
    When I click "Fórum" in the "Elsődleges navigáció" menu block
    And I click "Új Fórumtéma hozzáadása"
    Then I should see an "form[data-drupal-selector='node-forum-form']" element

  @javascript
  Scenario: Forum topic create - required fields
    Given I am on "/node/add/forum"
    And required attributes are removed from all input elements in form "node-forum-form"
    When I press "Mentés"
    Then I should see only the following error messages:
      | Tárgy mező szükséges.   |
      | Fórumok mező szükséges. |

  @javascript
  Scenario: Forum topic create - success
    Given I am on "/node/add/forum"
    When I select "Modulok fejlesztése" from "Fórumok"
    And I fill in "Tárgy" with "My topic 01"
    And I fill in wysiwyg on field "app_body[0][value]" with "<p>My body</p>"
    And I check the following checkboxes in the "Drupal verzió" checkbox group:
      | 9.x |
    And I press "Mentés"
    Then I should see 1 message
    And I should see only the following status message:
      | My topic 01 Fórumtéma létrejött. |
