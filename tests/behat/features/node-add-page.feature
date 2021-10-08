@api
Feature: Node add page.

  Scenario: Node add page - access - anonymous
    Given I am an anonymous user
    When I go to "/node/add"
    Then the response status code should be 403

  Scenario: Node add page - access - logged in
    Given I am logged in as an "authenticated"
    When I go to "/node/add"
    Then the response status code should be 200
