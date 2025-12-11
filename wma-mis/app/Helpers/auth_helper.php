<?php

// app/Helpers/database_helper.php

if (!function_exists('removeGroupBy')) {
  function removeGroupBy()
  {
      // Get the database connection
      $db = db_connect();

      // Check if ONLY_FULL_GROUP_BY is present in the current mode
      $query = $db->query("SELECT @@sql_mode as mode");
      $result = $query->getRow();

      $currentMode = $result->mode;

      if (strpos($currentMode, 'ONLY_FULL_GROUP_BY') !== false) {
          // Remove ONLY_FULL_GROUP_BY from the current mode
          $newMode = str_replace('ONLY_FULL_GROUP_BY', '', $currentMode);

          // Trim any extra commas or spaces
          $newMode = trim($newMode, ', ');

          // Update the SQL mode
          $db->query("SET sql_mode = ?", [$newMode]);

          return "ONLY_FULL_GROUP_BY removed from SQL mode.";
      } else {
          return "ONLY_FULL_GROUP_BY is not present in the SQL mode.";
      }
  }
}
