<?php

include('shared.inc.php');

function makeTSV($table) {
  global $link;

  header('Content-Type: text/plain');
  header("Content-Disposition: attachment; filename=\"$table.tsv\"");

  $result = mysqli_query($link, "SHOW COLUMNS FROM `$table`");

  while ($row = mysqli_fetch_row($result)) {
    $columns[] = $row[0];
  }
  array_pop($columns); // remove timestamp
  array_pop($columns); // remove createstamp
  echo implode("\t", $columns) . "\n";

  $result = mysqli_query($link, "SELECT * FROM `$table`");

  while ($row = mysqli_fetch_row($result)) {
    array_pop($row); // remove timestamp
    array_pop($row); // remove createstamp
  
    echo implode("\t", $row) . "\n";
  }
}

if ($_GET['file'] == 'gridrows') {
  makeTSV('gridrows');
} elseif ($_GET['file'] == 'grids') {
  makeTSV('grids');
} elseif ($_GET['file'] == 'companies') {
  makeTSV('companies');
} elseif ($_GET['file'] == 'jurisdictions') {
  makeTSV('jurisdictions');
} else {

?>
<!DOCTYPE html>
<html>
<head>
  <title>Project X-Ray / Download Data</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body style="margin: 12px;">

<p style="font-size: 18px; font-weight: bold;">
  Bulk Crowdsourced Data
</p>

<p>
  Data is provided in .tsv tab-separated format for anyone to use and analyze. Please cite Project X-Ray.
</p>

<ul>
  <li><a href="data.php?file=jurisdictions">Jurisdictions</a></li>
  <li><a href="data.php?file=companies">Companies</a></li>
  <li><a href="data.php?file=grids">Grids</a></li>
  <li><a href="data.php?file=gridrows">Grid Rows</a></li>
</ul>

<p style="font-size: 18px; font-weight: bold;">
  Countries in the Data
</p>

<?php

$result = mysqli_query($link, "SELECT `country`, COUNT(*) AS `count` FROM `gridrows` GROUP BY `country`");

if (mysqli_num_rows($result)) {
  echo "<ol>\n";
  while ($row = mysqli_fetch_assoc($result)) {
    if (! empty($row['country'])) {
      echo "  <li><a href=\"table.php?country=" . $row['country'] . "\">" . $row['country'] . "</a> (" . $row['count'] . ")</li>\n";
    } else {
      echo "  <li>No Country Listed (" . $row['count'] . ")</li>\n";
    }
  }
  echo "</ol>\n\n";
} else {
  echo "<p>\n";
  echo "  No data available yet.\n";
  echo "</p>\n\n";
}

?><p style="text-align: center; padding-top: 24px;">
  <a href="index.php">Home</a> | <a href="go.php">Enter Data</a>
</p>

</body>
</html>

<?php

}

?>
