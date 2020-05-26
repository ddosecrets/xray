<?php

// The following four lines should be updated before deploying
define('IMAGEPATH', 'https://xray.ddosecrets.com/images/');
define('FILEPATH', 'install_path/images/');
define('TOOLSPATH', 'install_path/bin/');
$link = mysqli_connect('localhost', 'root', '', '', 32077, '');

session_name('grids');
session_start();


function ownershipTable($row, $gridrow)
{
  global $link;

  if ($gridrow['confirmed'] < 3) {
    echo "<p>\n";
    echo "This data has not yet been validated.\n";
    echo "</p>\n\n";
  }

  echo "<table style=\"width: 100%; border: 1px solid black;\">\n\n";

  echo "<tr>\n";
  echo "<td></td>\n";
  echo "<td><b>Name / Company</b></td>\n";
  echo "<td><b>Address</b></td>\n";
  echo "<td><b>Start Date</b></td>\n";
  echo "<td><b>End Date</b></td>\n";
  echo "<td><b>Position</b></td>\n";
  echo "<td><b>Type (Officer/Director)</b></td>\n";
  echo "</tr>\n\n";

  $subresult = mysqli_query($link, "SELECT * FROM `gridrows` WHERE `gridid`='" . $gridrow['gridid'] . "'");

  while ($subrow = mysqli_fetch_assoc($subresult)) {
    if (! empty($subrow['companyname'])) {
      $name = $subrow['companyname'];
    } else { 
      $name = $subrow['firstname'] . ' ' . $subrow['middlename'] . ' ' . $subrow['lastname'];
    }
    if (! empty($subrow['idnumber'])) {
      $name .= "<br>\n";
      $name .= "(ID " . $subrow['idnumber'] . ")";
    }

    $address = '';
    if (! empty($subrow['address1']))
      $address .= $subrow['address1'];
    if (! empty($subrow['address2'])) {
      $address .= "<br>\n";
      $address .= $subrow['address2'];
    }
    if (! empty($subrow['address3'])) {
      $address .= "<br>\n";
      $address .= $subrow['address3'];
    }
    if (! empty($subrow['city']) || ! empty($subrow['stateprovince']) || ! empty($subrow['postalcode'])) {
      if (! empty($subrow['address1']) || ! empty($subrow['address2']) || ! empty($subrow['address3']))
      $address .= "<br>\n";
      $address .= $subrow['city'] . ($subrow['stateprovince'] || $subrow['postalcode'] ? ', ' : '') . $subrow['stateprovince'] . ' ' . $subrow['postalcode'];
    }
    if (! empty($subrow['country'])) {
      $address .= "<br>\n";
      $address .= strtoupper($subrow['country']);  
    }
    $startDate = $subrow['startdate'] != '0000-00-00' ? $subrow['startdate'] : '';
    $endDate = $subrow['enddate'] != '0000-00-00' ? $subrow['enddate'] : '';
    $position = $subrow['position'];
    $type = $subrow['type'];

    echo "<tr>\n";
    echo "<td><a href=\"go.php?gridid=" . $subrow['gridid'] . "\">View</a></td>\n";
    echo "<td>$name</td>\n";
    echo "<td>$address</td>\n";
    echo "<td>$startDate</td>\n";
    echo "<td>$endDate</td>\n";
    echo "<td>$position</td>\n";
    echo "<td>$type</td>\n";
    echo "</tr>\n\n";
  }

  echo "<tr>\n";
  echo "<td colspan=7>Recorded by the " . $row['jurisdiction'] . " registry on " . $gridrow['date'] . "<br>\n";
  echo "Digitally crowdsourced on " . $gridrow['createstamp'] . "</td>\n";
  echo "</tr>\n\n";

  echo "</table>";
}

?>
