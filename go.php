<?php

include('shared.inc.php');

function nextScreen()
{
  global $link;

  if (! empty($_GET['gridid'])) {
    $snippet = "WHERE `grids`.`gridid`='" . intval($_GET['gridid']) . "'";
  } else {
    $snippet = "ORDER BY RAND() LIMIT 0, 1";
  }

  $result = mysqli_query($link, "SELECT `grids`.`gridid`, `companies`.`companyid`, `companies`.`name`, `companies`.`type`, `companies`.`number`, `grids`.`document`, `grids`.`page`, `grids`.`date` AS `submissiondate` FROM `grids` LEFT JOIN `companies` ON `grids`.`companyid`=`companies`.`companyid` $snippet;");

  $row = mysqli_fetch_assoc($result);
  $gridid = $row['gridid'];
  $_SESSION['grids'][$gridid] = $row;

  $result = mysqli_query($link, "SELECT * FROM `gridrows` WHERE `gridid`='$gridid' ORDER BY `order`");

  $i = 0;
  while ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['grids'][$gridid]['rows'][$i] = $row;
    $i++;
  }

  $result = mysqli_query($link, "SELECT SUM(IF(`confirmed`>=1,1,0)) AS `pending`, SUM(IF(`confirmed`>=3,1,0)) AS `complete`, COUNT(`gridid`) AS `total` FROM `grids`");

  $_SESSION['stats'] = mysqli_fetch_assoc($result);

  return $gridid;
}

$fields = array('firstname', 'middlename', 'lastname', 'nationality', 'idnumber', 'companyname', 'address1', 'address2', 'address3', 'city', 'stateprovince', 'postalcode', 'country', 'position', 'type', 'startdate', 'enddate', 'status');
$numberFields = count($fields);

$success = FALSE;
if (! empty($_POST['nodata'])) {
  $result = mysqli_query($link, "UPDATE `grids` SET `confirmed`='-1' WHERE `gridid`='" . intval($_POST['gridid']) . "'");

  $gridid = nextScreen();
} elseif (count($_POST)) {
  $gridid = $_POST['gridid'];
  $numberRows = count($_POST['rowid']);

  if (! preg_match('/\d\d\d\d-\d\d-\d\d/', $_POST['submissiondate'])) {
    $errors[] = 'Please fill in the submission (stamp) date.';
  }

  for ($i = 0; $i < $numberRows; $i++) {
    if (! empty($_POST['startdate'][$i]) && ! preg_match('/\d\d\d\d-\d\d-\d\d/', $_POST['startdate'][$i]))
      $errors[] = 'There is a problem with the start date in row ' . ($i + 1) . '.';
    if (! empty($_POST['enddate'][$i]) && ! preg_match('/\d\d\d\d-\d\d-\d\d/', $_POST['enddate'][$i]))
      $errors[] = 'There is a problem with the end date in row ' . ($i + 1) . '.';

    $allEmpty = $i;
    for ($j = 0; $j < $numberFields; $j++) {
      if (! empty(trim($_POST[$fields[$j]][$i]))) {
        $allEmpty = FALSE;
      }
    }
    if ($allEmpty !== FALSE)
      $errors[] = 'Please fill in at least one field in row ' . ($allEmpty + 1) . ($allEmpty + 1 > 1 ? ' or delete the row' : '') . '.';
  }    

  if (! count($errors)) {
    // Trying to save and everything is fine.

    $result = mysqli_query($link, "UPDATE `grids` SET `date`='" . $_POST['submissiondate'] . "', `confirmed`=`confirmed`+1 WHERE `gridid`='" . intval($_POST['gridid']) . "' LIMIT 1");

    // INSERT new rows

    $insertSQL = "INSERT INTO `gridrows` (`gridid`, ";
    for ($j = 0; $j < $numberFields; $j++) {
      $insertSQL .= '`' . $fields[$j] . '`, ';
    }
    $insertSQL .= "`order`, `timestamp`, `createstamp`) VALUES ";
    $insertSQLBody = array();
    for ($i = 0; $i < $numberRows; $i++) {
        if (empty($_POST['startdate'][$i]))
          $_POST['startdate'][$i] = '0000-00-00';
        if (empty($_POST['enddate'][$i]))
          $_POST['enddate'][$i] = '0000-00-00';

      $o = '';
      if (empty($_POST['rowid'][$i])) {
        $o .= "('" . intval($_POST['gridid']) . "', ";
        for ($j = 0; $j < $numberFields; $j++) {
          $o .= "'" . mysqli_real_escape_string($link, strip_tags($_POST[$fields[$j]][$i])) . "', ";
        }
        $o .= "'" . ($i + 1) . "', NOW(), NOW())";
      }
      if (strlen($o))
        $insertSQLBody[] = $o;
    }

    if (count($insertSQLBody)) {
      $result = mysqli_query($link, $insertSQL . implode(', ', $insertSQLBody));
      if (mysqli_errno($link)) {
        //echo "SQL Error: " . mysqli_errno($link) . ': ' . mysql_error() . "<BR>\n";
	echo "SQL Error: " . mysqli_error($link) . "<BR>\n";
	error_log("SQL Error: " . mysqli_error($link) . "<BR>\n");
	error_log("Insert statement was: " . $insertSQL . implode(', ', $insertSQLBody) . "<BR>\n");
	error_log("Dumped POST request:<BR>\n" . print_r($_POST, true));
        die;
      }
    }

    // UPDATE existing rows

    for ($i = 0; $i < $numberRows; $i++) {
      $updateSQLBody = '';
      $updateSQL = "UPDATE `gridrows` SET ";

      if (! empty($_POST['rowid'][$i])) {
        for ($j = 0; $j < $numberFields; $j++) {
          if ($j)
            $updateSQLBody .= ', ';
          $updateSQLBody .= '`' . $fields[$j] . "`='" . mysqli_real_escape_string($link, strip_tags($_POST[$fields[$j]][$i])) . "'";
        }
      }

      if (strlen($updateSQLBody)) {
        $updateSQL .= $updateSQLBody . " WHERE `rowid`='" . (int)$_POST['rowid'][$i] . "' LIMIT 1";

        $result = mysqli_query($link, $updateSQL);
        // echo $updateSQL  . "<BR>\n";
        // echo mysql_error() . "<BR>\n";
      }
    }

    $success = TRUE;
  } else {
    // Trying to save but there are errors.
    for ($i = 0; $i < $numberRows; $i++) {
      for ($j = 0; $j < $numberFields; $j++) {
        $_SESSION['grids'][$_POST['gridid']]['rows'][$i][$fields[$j]] = $_POST[$fields[$j]][$i];
      }
    }
    $_SESSION['grids'][$_POST['gridid']]['submissiondate'] = $_POST['submissiondate'];
  }
} else {
  $gridid = nextScreen();
}

$grid  = $_SESSION['grids'][$gridid];
$image = $grid['type'] . $grid['number'] . '-' . $grid['document'] . '-' . ($grid['page'] - 1) . '.gif';

if ($success) {
  include('success.form.php');
} else {
  include('go.form.php');
}

?>
