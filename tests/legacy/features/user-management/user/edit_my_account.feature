@javascript
Feature: Change my profile
  In order to change my profile
  As an administrator
  I need to be able to change my profile informations

  Background:
    Given the "apparel" catalog configuration
    And I am logged in as "Peter"

  Scenario: Successfully change avatar
    Given I edit the "admin" user
    When I attach file "akeneo.jpg" to "Avatar"
    And I save the user
    Then I should see the flash message "User saved"
    And I should not see the default avatar

  @jira https://akeneo.atlassian.net/browse/PIM-8286
  Scenario: I can edit my own profile even if I don't have the permission to edit users
    Given I am on the "Administrator" role page
    And I visit the "Permissions" tab
    And I revoke rights to resources Edit users
    And I save the role
    When I edit the "Peter" user
    And I visit the "Groups and roles" tab
    Then the fields User groups and Roles should be disabled
    And I visit the "General" tab
    And I fill in the following information:
      | Middle name | James |
    And I save the user
    Then I should not see the text "There are unsaved changes"
    And the "Middle name" field should contain "James"

  @jira https://akeneo.atlassian.net/browse/PIM-6894
  Scenario: Successfully update my password with any characters
    Given I edit the "Peter" user
    When I visit the "Password" tab
    And I fill in the following information:
      | Current password      | Peter         |
      | New password          | Peter{}()/\@: |
      | New password (repeat) | Peter{}()/\@: |
    And I save the user
    Then I should not see the text "There are unsaved changes"
    When I logout
    And I am logged in as "Peter" with password Peter{}()/\@:
    Then I am on the dashboard page

  @jira https://akeneo.atlassian.net/browse/PIM-6914
  Scenario: Successfully display the UI locale of the user
    Given I edit the "Peter" user
    When I visit the "Interfaces" tab
    And I fill in the following information:
      | UI locale (required) | French (France) |
    And I save the user
    Then I should not see the text "There are unsaved changes"
    And I should see the text "français (France)"
    Given I edit the "Mary" user
    When I visit the "Interfaces" tab
    And I fill in the following information:
      | Langue de l'interface (obligatoire) | français (France) |
    And I save the user
    Then I should not see the text "There are unsaved changes"
    And I should see the text "français (France)"
