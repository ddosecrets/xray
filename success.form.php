<!DOCTYPE html>
<html>
  <head>
    <title>Project X-Ray / <?php echo $grid['type'] . $grid['number'] . ' - ' . $grid['name']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/jquery-ui.min.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  </head>
  <body style="overflow: hidden">
    <style>td { border: 1px solid black; padding: 4px; }</style>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="container-fluid vertical-top">
          <div class="row-fluid btn-toolbar text-center">
            <div class="btn-group pull-left">
              <button class="btn btn-small" type="button" style="margin-right: 8px;" onclick="window.location='index.php';">
                <b>&#127968;</b>
              </button>
            </div>
            <?php echo "<a class=\"btn btn-link\" href=\"table.php?companyid=" . $grid['companyid'] . "\"><b>" . $grid['type'] . $grid['number'] . ' - ' . $grid['name'] . "</b></a>"; ?>
            <button class="btn btn-link pull-right" style="padding-left: 0px; padding-right: 0px;" type="button" onclick="next()">
              <?php echo number_format($_SESSION['stats']['pending']) . ' of ' . number_format($_SESSION['stats']['total']); ?>  In Progress! Next &rarr;
            </button>
          </div>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span12 vertical-bottom">
          <p style="font-size: 24px; font-weight: bold; text-align: center;">
            Success!
          </p>
          <p>
            Once this grid has been verified by two other users, it will be finalized and publicly viewable.
          </p>
          <?php

          $result = mysqli_query($link, "SELECT * FROM `grids` WHERE `gridid`='" . intval($_POST['gridid']) . "'");

          if (mysqli_num_rows($result)) {
            while ($gridrow = mysqli_fetch_assoc($result)) {
              echo "<p>\n";
              echo "  Document "  . $gridrow['document'] . ", Page " . $gridrow['page'];
              echo "</p>";

              ownershipTable($row, $gridrow);
            }
          }

          ?>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span4 horizontal-left" hidden></div>
        <div class="span8 horizontal-right" hidden></div>
      </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.rotate.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/go.min.js"></script>
  </body>
</html>