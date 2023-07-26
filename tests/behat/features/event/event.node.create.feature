@api
@javascript
#noinspection NonAsciiCharacters
Feature: Event node create

  Background:
    Given users:
      | name    | mail                    | roles         |
      | dummy-1 | dummy-1@behat.localhost | administrator |
    And I am logged in as "dummy-1"
    And I am at "/node/add/event"

  Scenario: Event node create – required field messages
    Given required attributes are removed from all input elements in form "node-event-form"
    And I press "Mentés"
    Then I should see 3 message
    Then I should see only the following error messages:
      | Cím mező szükséges.      |
      | Bevezető mező szükséges. |
      | Törzs mező szükséges.    |

  Scenario: Event node create – only required fields
    When I fill in "Cím" with "My event 01"
    And I fill in wysiwyg on field "app_teaser[0][value]" with "My teaser text"
    And I fill in wysiwyg on field "app_body[0][value]" with "My body text"
    And I press "Mentés"
    Then I should see 1 message
    And I should see only the following status message:
      | My event 01 Esemény létrejött. |

  Scenario: Event node create – all fields
    When I fill in "Cím" with "My event 01"
    And I open the media library browser of the "Kép" media field
    And I attach the file "behat-01-1440x1080.jpg" to "files[upload][]"
    And I wait for AJAX to finish
    And I fill in "media[0][fields][field_media_image][0][alt]" with "My alt text"
    And I press the "Mentés" action button in the "Média hozzáadása vagy kiválasztása" dialog
    And I press the "Kijelölt beillesztése" action button in the "Média hozzáadása vagy kiválasztása" dialog
    And I fill "Dátum" date range field with:
      | from      | to        |
      | +1 months | +3 months |
    And I fill in wysiwyg on field "app_teaser[0][value]" with "My teaser text"
    And I fill in wysiwyg on field "app_body[0][value]" with "My body text"
    And I fill "Linkek" link field with:
      | uri                 | title      |
      | https://example.org | My link 01 |
      | https://drupal.org  | My link 02 |
    And I attach the file "behat-01-1440x1080.jpg" to "files[app_attachments_0][]"
    And I press "Mentés"
    Then I should see 1 message
    And I should see only the following status message:
      | My event 01 Esemény létrejött. |
