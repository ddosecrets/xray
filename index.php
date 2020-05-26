<?php

include('shared.inc.php');

$result = mysqli_query($link, "SELECT SUM(IF(`confirmed`>=1,1,0)) AS `pending`, SUM(IF(`confirmed`>=3,1,0)) AS `complete`, COUNT(`gridid`) AS `total` FROM `grids`");

$_SESSION['stats'] = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html>
<head>
  <title>Project X-Ray</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body style="margin: 36px;">

<p style="text-align: center;">
  <img src="img/xray.gif" alt="Project X-Ray" style="padding: 24px;">
</p>

<p style="font-size: 30px; font-weight: bold; text-align: center; padding-bottom: 24px;">
  Project X-Ray
</p>

<p>
  Project X-Ray is a crowdsourced effort sponsored by <a href="https://ddosecrets.com">Distributed Denial of Secrets</a> to identify the owners of offshore corporations who are contributing to global inequality. In many cases these corporate owners may have committed crimes beyond mere tax evasion. Many, but not all, of these offshore corporations file confidential updates to their respective corporate registries containing information on their officers and directors. Many of those secret documents are now available on this site. While the listed companies and individuals are often not the Ultimate Beneficial Owner (or UBO) of the offshore corporation, this information can still provide important clues to journalists and law enforcement officials.
</p>

<p>
  Unfortunately, much of the information provided is not offered in any standard format, and is scanned, making typical Optical Character Recognition (OCR) technology a poor solution. And without functioning OCR, the information is impossible to search. That's where you come in: with enough volunteers around the world, the information can be <a href="https://www.occrp.org/en/panamapapers/the-source-of-the-panama-papers-speaks/">digitized</a>, standardized and verified using good old-fashioned typing.
</p>

<p>
  So far, out of <?php echo number_format($_SESSION['stats']['total']); ?> grids from <?php echo 1; ?> jurisdiction, <?php echo number_format($_SESSION['stats']['pending']); ?> grids are pending verification (<?php echo number_format($_SESSION['stats']['pending'] / $_SESSION['stats']['total'] * 100, 2); ?>%), and <?php echo number_format($_SESSION['stats']['complete']); ?> are completely verified (<?php echo number_format($_SESSION['stats']['complete'] / $_SESSION['stats']['total'] * 100, 2); ?>%).
</p>

<p>
  Contribute as much or as little as you want. There is no sign up process. All data submitted is <a href="data.php">freely available to the global community</a>. <b>For your security and safety, we highly recommend using <a href="http://www.torproject.org">Tor</a> when accessing this site.</b>
</p>

<p style="font-size: 18px; text-align: center; padding-top: 24px;">
  <a href="go.php">Ready? Go!</a>
</p>

</body>
</html>
