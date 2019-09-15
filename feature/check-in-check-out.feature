Feature: People can check in and out of a building

  Scenario: People can check into a building
    Given a building
    When "bob" checks into the building
    Then "bob" should have been checked into the building

  Scenario: Double check-in interactions are detected and logged
    Given a building
    And "bob" checked into the building
    When "bob" checks into the building
    Then "bob" should have been checked into the building
    And a check-in anomaly should have been detected
