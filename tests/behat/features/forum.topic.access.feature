Feature: Forum topic related access checks

  Scenario: As an anonymous user I am not allowed to create a new forum topic
    Given I am an anonymous user
    When I am on "/node/add/forum"
    Then I should get a 403 HTTP response

  @api
  Scenario: As an authenticated user I should be able to create a new forum topic
    Given users:
      | name    | mail                    |
      | dummy-1 | dummy-1@behat.localhost |
    And I am logged in as "dummy-1"
    When I am on "/node/add/forum"
    Then I should get a 200 HTTP response
    And I should see an "form[data-drupal-selector='node-forum-form']" element
