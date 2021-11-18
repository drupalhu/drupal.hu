@api
@javascript
#noinspection NonAsciiCharacters
Feature: Personal contact form

  Background:
    Given users:
      | name         | mail                |
      | recipient-01 | recipient@localhost |

  Scenario: Personal contact form – navigation – anonymous
    Given I am an anonymous user
    When I go to the "canonical" page of "recipient-01" "user"
    Then I should not see any primary tabs

  Scenario: Personal contact form – access – anonymous
    Given I am an anonymous user
    When I go to the "canonical/contact" page of "recipient-01" "user"
    Then the response status code should be 403
    And the url should match "^/user/\d+/contact$"

  Scenario: Personal contact form – navigation – authenticated
    Given I am acting as a user with the "authenticated" role
    When I go to the "canonical" page of "recipient-01" "user"
    Then I should see the following primary tabs:
      | Megtekintés (aktív fül) |
      | Kapcsolat               |

    When I click "Kapcsolat" primary tab
    Then I should see an "form[data-drupal-selector='contact-message-personal-form']" element
