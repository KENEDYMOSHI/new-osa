<?php



function dateFormatter($actualDate)
{
  $date = strtotime($actualDate);
  return date("d M Y", $date);
}
function dateTimeFormatter($actualDate)
{
    $date = strtotime($actualDate);
    return date("d-m-Y  Hi", $date); 
}
function timeDate($actualDate)
{
  $date = strtotime($actualDate);
  return date("d M Y h:i A", $date);
}

function nextVerification($activity)
{
  // Get the current date
  $currentDate = new DateTime();

  // Array of activities that require adding 5 years
  $activities = [setting('Gfs.fst'), setting('Gfs.bst')];

  // Check if $activity is in the $activities array
  if (in_array($activity, $activities)) {
    // Add 5 years if $activity is in the array
    $currentDate->add(new DateInterval('P5Y'));
  } else {
    // Add 1 year if $activity is not in the array
    $currentDate->add(new DateInterval('P1Y'));
  }

  // Return the modified date
  return $currentDate->format('Y-m-d');
}


function timeDifference($t1, $t2)
{
  $t1To24 = date("H:i", strtotime($t1));
  $t2To24 = date("H:i", strtotime($t2));
  $time1 = strtotime($t1To24);
  $time2 = strtotime($t2To24);
  $difference =  (($time1 - $time2) / 60) / 60;

  return abs((int)$difference);
}

function to24Hours($time)
{
  return  date("H:i", strtotime($time));
}



if (!function_exists('overrideSqlMode')) {
  function overrideSqlMode()
  {
    // Get the database connection
    $db =  \Config\Database::connect();
    $db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
  }
}
