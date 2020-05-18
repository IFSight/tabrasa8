@api
Feature: Module version testing
  Scenario Outline: Test for correct module versions
    Given I am logged in as a user with the "administer modules" permission
    When I go to "admin/modules"
    Then I should get a 200 HTTP response
    And I should see the text "<version>" in the "<module_name>" row

    Examples:
    | version                 | module_name                 |
    | Version: 8.9.0-beta3    | Machine name: system        |
    | Version: 8.x-1.0-rc     | Machine name: smtp          |
    | Version: 8.x-5.13       | Machine name: webform       |
