@admin @managing_layouts @javascript
Feature: Duplicating a layout
    In order to create a slightly different variant of a page
    As an administrator
    I need to be able to duplicate a layout

    Background:
        Given there is a layout called "Example layout"
        And I am logged in as an administrator

    Scenario: Duplicating a layout
        When I duplicate a layout called "Example layout" with name "Copy of example layout"
        Then a layout called "Copy of example layout" should exist

    Scenario: Duplicating a layout and cancelling
        When I duplicate a layout called "Example layout" and cancel copying
        Then a layout called "Copy of example layout" should not exist

    Scenario: Duplicating a layout with existing name
        Given there is a layout called "Existing layout"
        When I duplicate a layout called "Example layout" with name "Existing layout"
        Then I should get an error saying "Layout with provided name already exists."
