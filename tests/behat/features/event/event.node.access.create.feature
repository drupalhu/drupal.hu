@api
@javascript
Feature: Event node access create

  Scenario: Event node access create – anonymous
    Given I am an anonymous user
    When I go to "/node/add/event"
    Then the response status code should be 403

  Scenario: Event node access create – authenticated
    Given users:
      | name    | mail                    |
      | dummy-1 | dummy-1@behat.localhost |
    And I am logged in as "dummy-1"
    When I go to "/node/add/event"
    Then the response status code should be 403

  Scenario: Event node access create – administrator
    Given users:
      | name    | mail                    | roles         |
      | dummy-1 | dummy-1@behat.localhost | administrator |
    And I am logged in as "dummy-1"
    When I go to "/node/add/event"
    Then the response status code should be 200
