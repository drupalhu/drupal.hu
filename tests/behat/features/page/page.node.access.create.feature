@api
@javascript
Feature: Page node access create

  Scenario Outline: node:page access:create with different roles
    Given I am acting as a user with the "<role>" roles
    When I go to "/node/add/page"
    Then the response status code should be <status_code>
    Examples:
      | role          | status_code |
      | anonymous     | 403         |
      | authenticated | 403         |
      | administrator | 200         |
