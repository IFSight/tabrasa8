Feature: Main Page Loading
  Scenario: Make sure the home page loads
    Given I am on the homepage
    Then I should get a 200 HTTP response
