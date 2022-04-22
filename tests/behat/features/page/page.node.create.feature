@api
@javascript
#noinspection NonAsciiCharacters
Feature: node:page create

  Background:
    Given users:
      | name    | mail                    | roles         |
      | dummy-1 | dummy-1@behat.localhost | administrator |
    And I am logged in as "dummy-1"
    And I am at "/node/add/page"

  Scenario: Page node create – required field messages
    Given required attributes are removed from all input elements in form "node-page-form"
    And I press "Mentés"
    Then I should see 1 message
    Then I should see only the following error messages:
      | Cím mező szükséges. |

  Scenario: Page node create – only required fields
    When I fill in "Cím" with "My page 01"
    And I press "Mentés"
    Then I should see 1 message
    And I should see only the following status message:
      | My page 01 Oldal létrejött. |

  @transliterate
  Scenario: Page node create – all fields
    When I fill in "Cím" with "My page 01"

    And I open the media library browser of the "Kép" media field
    And I attach the file "behat-01-1440x1080.jpg" to "files[upload]"
    And I wait for AJAX to finish
    And I fill in "media[0][fields][field_media_image][0][alt]" with "My alt text"
    And I press the "Mentés" action button in the "Média hozzáadása vagy kiválasztása" dialog
    And I press the "Kijelölt beillesztése" action button in the "Média hozzáadása vagy kiválasztása" dialog

    And I fill in wysiwyg on field "app_body[0][value]" with "My body text"

    # @todo File attach does not work at normal speed. It works with XDebug step debugger.
    # I have spent a lot of time to debugging, but still no clue what the problem is.
    # I narrowed down the problem to \DMore\ChromeDriver\ChromeDriver::attachFile()
    # $this->page->send().
    # But maybe there is a problem with the "I fill in wysiwyg on field ..." step.
    # The "I wait for AJAX to finish" step doesn't solve the problem.
    And I wait 2 seconds
    And I attach the file "árvíztűrő tükörfúrógép-lower.pdf" to "files[app_attachments_0][]"
    And I wait for AJAX to finish
    And I attach the file "ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP-upper.pdf" to "files[app_attachments_1][]"
    And I wait for AJAX to finish

    And I press "Mentés"

    Then I should see 1 message
    And I should see only the following status message:
      | My page 01 Oldal létrejött. |
    And I should see the text "arvizturo-tukorfurogep-lower"
    And I should see the text "arvizturo-tukorfurogep-upper"
