<!DOCTYPE html>
<html>
  <head>
    <title>Project X-Ray / <?php echo $grid['type'] . $grid['number'] . ' - ' . $grid['name']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/jquery-ui.min.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  </head>
  <body style="overflow: hidden">
    <style>
    .hint { border-bottom: 1px dotted #000000; cursor: help; position: relative; text-decoration: none; color: #000000;}
    .hint:hover { text-decoration: none; }
    </style>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="container-fluid vertical-top">
          <div class="row-fluid btn-toolbar text-center">
            <div class="btn-group pull-left">
              <button class="btn btn-small" type="button" style="margin-right: 8px;" onclick="window.location='index.php';">
                <b>&#127968;</b>
              </button>
              &nbsp;
              <button class="btn btn-small" type="button" onclick="rotate(90)">
                <b>&#8635;</b>
              </button>
              <button class="btn btn-small" type="button" onclick="rotate(-90)">
                <b>&#8634;</b>
              </button>
            </div>
            <div class="btn-group pull-left">
              <button class="btn btn-small btn-vertical" type="button" onclick="vertical()" disabled>
                <big>&#8942;</big>
              </button>
              <button class="btn btn-small btn-horizontal" type="button" onclick="horizontal()">
                <big>&#8943;</big>
              </button>
            </div>
            <?php echo "<a class=\"btn btn-link\" href=\"table.php?companyid=" . $grid['companyid'] . "\"><b>" . $grid['type'] . $grid['number'] . ' - ' . $grid['name'] . "</b></a>"; ?>
            <button class="btn btn-link pull-right" style="padding-left: 0px; padding-right: 0px;" type="button" onclick="next()">
              <?php echo number_format($_SESSION['stats']['pending']) . ' of ' . number_format($_SESSION['stats']['total']); ?>  In Progress! Next &rarr;
            </button>
          </div>
          <div class="row-fluid" style="overflow-x: hidden; overflow-y: scroll">
            <div>
              <img src="<?php echo IMAGEPATH . '1/' . $grid['type'] . '/' . $image; ?>" class="img-polaroid" style="width: 100%; width: calc(100% - 10px)">
            </div>
          </div>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span12 vertical-bottom">
          <form class="container-fluid" name="grids" action="go.php" method="post">
            <input type="hidden" name="gridid" value="<?php echo $grid['gridid']; ?>">
            <input type="hidden" name="nodata" id="nodata" value="0">
            <div class="row-fluid btn-toolbar text-right">
              <label class="pull-left data-fields">
                <b><a title="The date stamped on the document by the registry, or next to a signature." class="hint">Submission (Stamp) Date</a></b>&nbsp;
                <input class="input-small" type="text" name="submissiondate" placeholder="YYYY-MM-DD" value="<?php echo $grid['submissiondate']; ?>">
              </label>
              <button class="btn btn-danger btn-editing" type="button" onclick="nodataFlag()">
                No Officer/Director Data
              </button>
              <button class="btn btn-primary btn-editing" type="button" onclick="save()">
                Save
              </button>
              <button class="btn btn-submitting" type="button" onclick="edit()">
                <i class="icon-edit"></i>
                Edit
              </button>
              <button class="btn btn-primary btn-submitting" type="submit">
                <i class="icon-ok icon-white"></i>
                Confirm
              </button>
            </div>

            <?php

            echo "<div id=\"errors\">";
            if ($errors) {
              
              echo "  <ul>\n";
              foreach ($errors as $key => $value) {
                echo "    <li>$value</li>\n";
              }
              echo "  </ul>\n";
            }
            echo "</div>\n\n";

            ?>
            <div class="row-fluid" style="overflow: scroll">
              <table class="table table-hover data-fields">
                <thead>
                  <tr>
                    <th class="span1">
                    </th>
                    <th class="span3">
                      <a title="The personal or corporate name of the officer or director, as written." class="hint">Name</a>
                    </th>
                    <th class="span2">
                      <a title="The partial or complete mailing address of the officer or director, as written." class="hint">Address</a>
                    </th>
                    <th class="span2">
                      <a title="The start date of the officer or director's term, not when the registry was notified." class="hint">Start Date</a>
                    </th>
                    <th class="span2">
                      <a title="The end date of the officer or director's term, not when the registry was notified." class="hint">End Date</a>
                    </th>
                    <th class="span2">
                      <a title="The position, occuptation or title of the officer or director." class="hint">Position</a>
                    </th>
                  </tr>
                </thead>
                <?php

                if (! isset($_SESSION['grids'][$gridid]['rows'][0]))
                  $_SESSION['grids'][$gridid]['rows'][0] = array();

                $rows = $_SESSION['grids'][$gridid]['rows'];
                $numberRows = count($rows);

                for ($i = 0; $i < $numberRows; $i++) {

                ?>
                <tbody>
                  <tr>
                    <td>
                      <input type="hidden" name="rowid[]" value="<?php echo $rows[$i]['rowid']; ?>">
                      <div class="btn-group">
                        <button class="btn btn-remove" type="button" onclick="remove(event.target)">
                          <b>&minus;</b>
                        </button>
                        <button class="btn btn-add" type="button" onclick="add(event.target)">
                          <b>+</b>
                        </button>
                      </div>
                    </td>
                    <td>
                      <input class="span5" type="text" name="firstname[]" placeholder="First" value="<?php echo $rows[$i]['firstname']; ?>">
                      <input class="span2" type="text" name="middlename[]" placeholder="Middle" value="<?php echo $rows[$i]['middlename']; ?>">
                      <input class="span5" type="text" name="lastname[]" placeholder="Last" value="<?php echo $rows[$i]['lastname']; ?>">
                      <input class="span6" type="text" name="nationality[]" placeholder="Nationality" value="<?php echo $rows[$i]['nationality']; ?>">
                      <input class="span6" type="text" name="idnumber[]" placeholder="Passport/ID No." value="<?php echo $rows[$i]['idnumber']; ?>">
                      <div class="span12 muted text-center">&mdash; or &mdash;</div>
                      <input class="span12" type="text" name="companyname[]" placeholder="Company" value="<?php echo $rows[$i]['companyname']; ?>">
                    </td>
                    <td>
                      <input class="span12" type="text" name="address1[<?php echo $i; ?>]" placeholder="Address 1" value="<?php echo $rows[$i]['address1']; ?>">
                      <input class="span12" type="text" name="address2[<?php echo $i; ?>]" placeholder="Address 2" value="<?php echo $rows[$i]['address2']; ?>">
                      <input class="span12" type="text" name="address3[<?php echo $i; ?>]" placeholder="Address 3" value="<?php echo $rows[$i]['address3']; ?>">
                      <input class="span6" type="text" name="city[<?php echo $i; ?>]" placeholder="City" value="<?php echo $rows[$i]['city']; ?>">
                      <input class="span3" type="text" name="stateprovince[<?php echo $i; ?>]" placeholder="State" value="<?php echo $rows[$i]['stateprovince']; ?>">
                      <input class="span3" type="text" name="postalcode[<?php echo $i; ?>]" placeholder="Postal Code" value="<?php echo $rows[$i]['postalcode']; ?>">
                      <select class="dropdown-toggle" name="country[<?php echo $i; ?>]">
                        <option value="">Country</option>
                        <?php

                        $countries = array('Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Channel Islands', 'Chile', 'People\'s Republic of China', 'Republic of China', 'Christmas Island', 'Cocos(Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Cook Islands', 'Costa Rica', 'Cote d\'Ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'French Polynesia', 'Gabon', 'The Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea - Bissau', 'Guyana', 'Haiti', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Isle of Man', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jersey', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'North Korea', 'South Korea', 'Kosovo', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macau', 'Macedonia', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar', 'Nagorno - Karabakh', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Turkish Republic of Northern Cyprus', 'Northern Mariana', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn Islands', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Barthelemy', 'Saint Helena', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Martin', 'Saint Pierre and Miquelon', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'Somaliland', 'South Africa', 'South Ossetia', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Svalbard', 'Swaziland', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor - Leste', 'Togo', 'Tokelau', 'Tonga', 'Transnistria Pridnestrovie', 'Trinidad and Tobago', 'Tristan da Cunha', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam', 'British Virgin Islands', 'US Virgin Islands', 'Wallis and Futuna', 'Western Sahara', 'Yemen', 'Zambia', 'Zimbabwe');

			for ($j = 0; $j < count($countries); $j++) {
			  try {
			      echo "<option value=\"" . $countries[$j] . "\"" . ($rows[$i]['country'] == $countries[$j] ? ' selected' : '') . ">" . $countries[$j] . "</option>\n";
                          } catch(Exception $e) {} // Do nothing if key-error, just keep going
                        }

                        ?></select>
                      
                      <div class="span4 pull-right" style="margin-top: 4px; display: <?php echo ($i ? 'block' : 'none'); ?>"><a href="javascript:copyAbove(<?php echo $i; ?>);" id="copyFromAbove[<?php echo $i; ?>]" style="font-size: 10px;">Copy Above</a></div>
                    </td>
                    <td>
                      <input class="span12" type="text" name="startdate[]" placeholder="YYYY-MM-DD" value="<?php echo $rows[$i]['startdate']; ?>">
                    </td>
                    <td>
                      <input class="span12" type="text" name="enddate[]" placeholder="YYYY-MM-DD" value="<?php echo $rows[$i]['enddate']; ?>">
                      <label class="radio">
                        <input type="radio" name="status[<?php echo $i; ?>]" value=""<?php echo (empty($rows[$i]['status']) ? ' checked' : ''); ?>>No Remarks</label>
                      <label class="radio">
                        <input type="radio" name="status[<?php echo $i; ?>]" value="Resigned"<?php echo ($rows[$i]['status'] == 'Resigned' ? ' checked' : ''); ?>>Resigned</label>
                      <label class="radio">
                        <input type="radio" name="status[<?php echo $i; ?>]" value="Retired"<?php echo ($rows[$i]['status'] == 'Retired' ? ' checked' : ''); ?>>Retired</label>
                      <label class="radio">
                        <input type="radio" name="status[<?php echo $i; ?>]" value="Replaced"<?php echo ($rows[$i]['status'] == 'Replaced' ? ' checked' : ''); ?>>Replaced</label>
                      <label class="radio">
                        <input type="radio" name="status[<?php echo $i; ?>]" value="Not Re-Elected"<?php echo ($rows[$i]['status'] == 'Not Re-Elected' ? ' checked' : ''); ?>>Not Re-Elected</label>
                    </td>
                    <td>
                      <input class="span12" type="text" name="position[<?php echo $i; ?>]" placeholder="Position" value="<?php echo $rows[$i]['position']; ?>">
                      <label class="radio">
                        <input type="radio" name="type[<?php echo $i; ?>]" value="Officer"<?php echo ($rows[$i]['type'] == 'Officer' ? ' checked' : ''); ?>>Officer</label>
                      <label class="radio">
                        <input type="radio" name="type[<?php echo $i; ?>]" value="Director"<?php echo ($rows[$i]['type'] == 'Director' ? ' checked' : ''); ?> onclick="fillDirector(<?php echo $i; ?>);">Director</label>
                      <label class="radio">
                        <input type="radio" name="type[<?php echo $i; ?>]" value="Both"<?php echo ($rows[$i]['type'] == 'Both' ? ' checked' : ''); ?>>Both</label>
                    </td>
                  </tr>
                </tbody>
                <?php

                }

                ?>
              </table>
            </div>
          </form>
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
