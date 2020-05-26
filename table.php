<?php

include('shared.inc.php');

if (! empty($_GET['documentid'])) {
  $result = mysqli_query($link, "SELECT `documentid`, `companies`.`type`, `companies`.`number`, CONCAT(`companies`.`type`,`companies`.`number`,'-',`documents`.`number`,'.tif') AS `filename` FROM `documents` LEFT JOIN `companies` ON `documents`.`companyid`=`companies`.`companyid` WHERE `documentid`='" . intval($_GET['documentid']) . "'");

  $row = mysqli_fetch_assoc($result);

  if (empty($row['documentid'])) {
    echo "Please choose a valid document.";
    die;
  }

  $folder = floor($row['number'] / 1000);

  $filename = FILEPATH . $row['type'] . '/' . $folder . '/' . $row['filename'];
  $pdf = str_replace('.tif', '.pdf', $filename);

  if (! is_file($pdf)) {
    $result = exec(TOOLSPATH . "convert $filename $pdf");

    if (is_file($pdf)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/pdf');
      header('Content-Disposition: attachment; filename=' . str_replace('.tif', '.pdf', $row['filename']));
      header('Content-Transfer-Encoding: binary');
      header('Connection: Keep-Alive');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      header('Content-Length: ' . filesize($pdf));

      readfile($pdf);
    } elseif (is_file($filename)) {
      header('Content-Description: File Transfer');
      header('Content-Type: image/tiff');
      header('Content-Disposition: attachment; filename=' . $row['filename']);
      header('Content-Transfer-Encoding: binary');
      header('Connection: Keep-Alive');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filename));

      readfile($filename);
    } else {
      echo "PDF not found.";
    }
    die;
  }
}

$result = mysqli_query($link, "SELECT `companyid`, `companies`.`name`, `type`, `number`, `jurisdictions`.`name` AS `jurisdiction` FROM `companies` LEFT JOIN `jurisdictions` ON `companies`.`jurisdictionid`=`jurisdictions`.`jurisdictionid` WHERE `companyid`='" . intval($_GET['companyid']) . "'");

$row = mysqli_fetch_assoc($result);

if (empty($row['companyid']) && empty($_GET['country'])) {
  echo "Please choose a valid company.";
  die;
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Project X-Ray</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body style="margin: 12px;">
<style>td { border: 1px solid black; padding: 4px; }</style>

<?php

if (! empty($_GET['companyid'])) {
  echo "<p style=\"font-size: 18px; font-weight: bold;\">" . $row['jurisdiction'] . ' Company ' . $row['type'] . $row['number'] . ' - ' . $row['name'] . "</p>\n\n";

  echo "<p>\n";
  echo "  Documents\n";
  echo "</p>";

  $result = mysqli_query($link, "SELECT * FROM `documents` WHERE `companyid`='" . intval($_GET['companyid']) . "'");

  if (mysqli_num_rows($result)) {
    while ($documentrow = mysqli_fetch_assoc($result)) {
      if ($documentrow['available'] == 'Y'){
        $openTag = "<A HREF=\"" . $_SERVER['PHP_SELF'] . "?documentid=" . $documentrow['documentid'] . "\">";
        $closeTag = "</A>";
      } else {
        $openTag = '';
        $closeTag = '';
      }

      echo "<p>\n";
      echo "  " . $documentrow['number'] . ". " . $openTag . $documentrow['description'] . $closeTag;
      echo "</p>";
    }
  }

  $result = mysqli_query($link, "SELECT * FROM `grids` WHERE `companyid`='" . intval($_GET['companyid']) . "' AND `confirmed`>0");

  if (mysqli_num_rows($result)) {
    while ($gridrow = mysqli_fetch_assoc($result)) {
      echo "<p>\n";
      echo "  Document "  . $gridrow['document'] . ", Page " . $gridrow['page'];
      echo "</p>";

      ownershipTable($row, $gridrow);
    }
  } else {
    echo "<p>\n";
    echo "  No data has been crowdsourced regarding this company at this time.";
    echo "</p>\n\n";
  }
} elseif (! empty($_GET['country'])) {
  echo "<p style=\"font-size: 18px; font-weight: bold;\">Mailing Addresses in " . strip_tags($_GET['country']) . "</p>\n\n";

  $subresult = mysqli_query($link, "SELECT `companies`.`type` AS `companytype`, `companies`.`number`, `companies`.`name`, `jurisdictions`.`name` AS `jurisdiction`, `gridrows`.* FROM ((`gridrows` LEFT JOIN `grids` ON `gridrows`.`gridid`=`grids`.`gridid`) LEFT JOIN `companies` ON `grids`.`companyid`=`companies`.`companyid`) LEFT JOIN `jurisdictions` ON `companies`.`jurisdictionid`=`jurisdictions`.`jurisdictionid` WHERE UPPER(`country`)='" . mysqli_real_escape_string($link, strtoupper($_GET['country'])) . "'");

  if (mysqli_num_rows($subresult)) {
    echo "<table style=\"width: 100%; border: 1px solid black;\">\n\n";

    echo "<tr>\n";
    echo "<td></td>\n";
    echo "<td><b>Offshore Entity</b></td>\n";
    echo "<td><b>Name / Company</b></td>\n";
    echo "<td><b>Address</b></td>\n";
    echo "<td><b>Start Date</b></td>\n";
    echo "<td><b>End Date</b></td>\n";
    echo "<td><b>Position</b></td>\n";
    echo "<td><b>Type (Officer/Director)</b></td>\n";
    echo "</tr>\n\n";

    while ($subrow = mysqli_fetch_assoc($subresult)) {
      $offshore = $subrow['jurisdiction'] . ' Entity ' . $subrow['companytype'] . $subrow['number'] . ' - ' . $subrow['name'];

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
      echo "<td>$offshore</td>\n";
      echo "<td>$name</td>\n";
      echo "<td>$address</td>\n";
      echo "<td>$startDate</td>\n";
      echo "<td>$endDate</td>\n";
      echo "<td>$position</td>\n";
      echo "<td>$type</td>\n";
      echo "</tr>\n\n";
    }

    echo "</table>";
  } else {
    echo "<p>\n";
    echo "  No addresses found.\n";
    echo "</p>\n\n";
  }
}

?>

<p style="text-align: center; padding-top: 24px;">
  <a href="index.php">Home</a> | <a href="data.php">Download Data</a> | <a href="go.php">Enter Data</a>
</p>

</body>
<html>
