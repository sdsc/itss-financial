<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Bootstrap Admin Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="../css/theme.bootstrap_2.css" rel="stylesheet">
    
    <link ref="../css/jquery.tablesorter.pager.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <?php require "sidebar.php" ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Bounce Tickets
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        View
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="#">All</a>
                                        </li>
                                        <li><a href="#">Changes</a>
                                        </li>
                                        <li><a href="#">Additions</a>
                                        </li>
                                        <li><a href="#">Deletions</a>
                                        </li>
                                        <li><a href="#">Modifications</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
<?php
    $connection = oci_connect("kdly", "viewRecharge2Day!", "dbh1.sdsc.edu:1521/business.sdsc.edu"); // connect to database
    $masterArray = array(); //an array of all the master ticket numbers
    /*$invalidReasons = []; // an associative array connecting the 
                          // ticket numbers with their invalid reasons
    $ticketData = []; // associative array connecting ticket #s with their data
    $masterData = []; // associative array connecting master ticket #s with data */
    $stid = oci_parse($connection, "SELECT * FROM recharge_pro.invalid_charges WHERE PROJECT_NUM=3");
    oci_execute($stid);

    // parse data from the invalid charges database
    while($array = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $ticketNum = $array["JOB_NUM"];
        $ticketIndex = $array["INDX"];
        //$invalidReasons[$ticketNum][$ticketIndex][] = $array["INVALID_REASON"]; // add invalid reason to array
        $invalidReasons[$ticketNum][$array["SELLER"]][$array["PROD_CODE"]][] = $array["INVALID_REASON"]; // add invalid reason to array
        /* fill in ticket data */
        $ticketData[$ticketNum]["seller"][] = $array["SELLER"];
        $ticketData[$ticketNum]["seller"] = array_unique($ticketData[$ticketNum]["seller"]);
        $ticketData[$ticketNum][$array["SELLER"]]["prodCode"][] = $array["PROD_CODE"];
        $ticketData[$ticketNum][$array["SELLER"]]["prodCode"] = array_unique($ticketData[$ticketNum][$array["SELLER"]]["prodCode"]);
        $ticketData[$ticketNum]["closeDate"] = $array["CLOSE_DATE"];
        $ticketData[$ticketNum]["pid"] = $array["PID"];
        $ticketData[$ticketNum]["buyer"] = $array["BUYER"];
        $ticketData[$ticketNum]["index"][] = $ticketIndex;
        $ticketData[$ticketNum]["index"] = array_unique($ticketData[$ticketNum]["index"]);
        $ticketData[$ticketNum][$ticketIndex]["indexPct"] = $array["INDX_CHARGE_PCT"];
        //$ticketData[$ticketNum]["indexPct"][] = $array["INDX_CHARGE_PCT"];
        $ticketData[$ticketNum][$ticketIndex]["rawQty"] = $array["RAW_QTY"];
        $ticketData[$ticketNum][$ticketIndex]["qty"] = $array["QTY"];
        $ticketData[$ticketNum]["rate"] = $array["RATE"];
        $ticketData[$ticketNum][$ticketIndex]["cost"] = $array["COST"];
            
        // get master ticket number
        $stid2 = oci_parse($connection, "SELECT MRREF_TO_MR, BILLABLE FROM footprints.master3 WHERE MRID=" . $ticketNum);
        oci_execute($stid2);
        $returnArray = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS);
        $ticketData[$ticketNum]["billable"] = $returnArray["BILLABLE"];
        $result = explode(" ", $returnArray["MRREF_TO_MR"]);
        $parent = $result[0]; // first ticket in the MRREF_TO_MR array, which should be the parent
        
        // if ticket has a parent
        if ($parent != null && $parent[0] == 'P') {
            $key = substr($parent, 1); // get rid of the prefix P in front of parent ticket #s
            
            // if master ticket does not exist or child ticket has not been added
            if (!array_key_exists($key, $masterArray) || !in_array($ticketNum, $masterArray[$key])) {
                $masterArray[$key][] = $ticketNum;
            }
        } else { // if ticket does not have parent; i.e. it is its own parent
            if (!array_key_exists($ticketNum, $masterArray) || !in_array($ticketNum, $masterArray[$ticketNum])) {
                $masterArray[$ticketNum][] = $ticketNum;
            }
        }
    }

    // get master ticket info
    foreach (array_keys($masterArray) as $value) {
        $stid3 = oci_parse($connection, "SELECT * FROM footprints.master3 WHERE MRID=" . $value);
        oci_execute($stid3);
        $returnArray = oci_fetch_array($stid3, OCI_ASSOC+OCI_RETURN_NULLS);
        $masterData[$value]["status"] = $returnArray["MRSTATUS"];
        $masterData[$value]["description"] = $returnArray["MRTITLE"];
        $masterData[$value]["billable"] = $returnArray["BILLABLE"];
        $masterData[$value]["closeDate"] = $returnArray["CLOSE__BDATE"];
        $masterData[$value]["approved"] = $returnArray["APPROVED__BBY__BMANAGER"];
        for ($i = 1; $i <= 10; $i++) {
            if ($returnArray["ITEM__B" . $i . "__BSELLER"] == null) {
                break;
            }
            $masterData[$value]["seller"][] = strtoupper(str_replace("__b", " ", $returnArray["ITEM__B" . $i . "__BSELLER"]));
            $masterData[$value]["rate"][] = str_replace("__d", ".", $returnArray["ITEM__B" . $i . "__BRATE"]);
            $masterData[$value]["qty"][] = $returnArray["ITEM__B" . $i . "__BQUANTITY"];
            $masterData[$value]["cost"][] = $returnArray["ITEM__B" . $i . "__BRATE"] * $returnArray["ITEM__B" . $i . "__BQUANTITY"];
        }

        for ($i = 1; $i <= 6; $i++) {
            if ($returnArray["INDEX__B" . $i] == null) {
                break;
            }
            $masterData[$value]["index"][] = $returnArray["INDEX__B" . $i];
            $masterData[$value]["indexPct"][] = $returnArray["PERCENT__B" . $i];
        }

        $stid3 = oci_parse($connection, "SELECT USER__BID, EMAIL__BADDRESS, PID, FIRST__BNAME, LAST__BNAME FROM footprints.master3_abdata WHERE MRID=" . $value);
        oci_execute($stid3);
        $returnArray = oci_fetch_array($stid3, OCI_ASSOC+OCI_RETURN_NULLS);
        $masterData[$value]["userid"] = $returnArray["USER__BID"];
        $masterData[$value]["email"] = $returnArray["EMAIL__BADDRESS"];
        $masterData[$value]["pid"] = $returnArray["PID"];
        $masterData[$value]["firstName"] = $returnArray["FIRST__BNAME"];
        $masterData[$value]["lastName"] = $returnArray["LAST__BNAME"];
    }

    echo '<h1> Affected Master Tickets </h1>';
    echo '<button id="applyChanges">Apply Changes</button>
<button onclick="expandAll()">Expand All</button><br><br><br>';
    $number = 0;
    $rowNum = 0;
    foreach (array_keys($masterArray) as $value) {
        // The expand button and the number of bounced tickets
        echo "<button class='expand' onclick='showChildren(" . $number . ")'>+</button>\n";
        echo "<span class='masterTicket'>Ticket #" . $value . " - " . count($masterArray[$value]) . " child issues.</span>";
        echo "<div class='masterTicketInfo' id=m" . $number . ">";
        echo "<b>Status: </b>" . $masterData[$value]["status"] . "<br>";
        echo "<b>Description: </b>" . $masterData[$value]["description"] . "<br>";
        echo "<b>User ID: </b>" . $masterData[$value]["userid"] . "<br>";
        echo "<b>Email: </b>" . $masterData[$value]["email"] . "<br>";
        echo "</div>";
        echo "<div class='children' id=p" . $number . ">";

        // The header for each table displaying the bounced tickets
        echo "<table border=1>\n<tr>\n<th>Seller</th>\n";
        echo "<th>Prod Code</th>\n";
        echo "<th>Ticket #</th>\n";
        echo "<th>Reason</th>\n";
        echo "<th><input type=\"text\" class=\"colHeader editable\" value=\"Close Date\" data-row=\"" . $rowNum . "\" data-column=\"closeDate\" data-original=\"Close Date\" data-master=\"" . $value . "\"></th>\n";
        echo "<th><input type=\"text\" class=\"colHeader editable\" value=\"PID\" data-row=\"" . $rowNum . "\" data-column=\"pid\" data-original=\"PID\" data-master=\"" . $value . "\"></th>\n";
        echo "<th><input type=\"text\" class=\"colHeader editable\" value=\"Buyer\" data-row=\"" . $rowNum . "\" data-column=\"buyer\" data-original=\"Buyer\" data-master=\"" . $value . "\"></th>\n";
        echo "<th><input type=\"text\" class=\"colHeader editable\" value=\"Index\" data-row=\"" . $rowNum . "\" data-column=\"index\" data-original=\"Index\" data-master=\"" . $value . "\"></th>\n";
        echo "<th>Index Pct</th>\n";
        echo "<th>Raw Qty</th>\n";
        echo "<th>Qty</th>\n";
        echo "<th>Rate</th>\n";
        echo "<th>Cost</th>\n";
        echo "<th><input type=\"text\" class=\"colHeader editable\" value=\"Billable\" data-row=\"" . $rowNum . "\" data-column=\"billable\" data-original=\"Billable\" data-master=\"" . $value . "\"></th>\n";
        echo "</tr>\n";

        // the first entry will always be the main master ticket
        // this is where the master ticket would go............
        $rowNum++;
        echo "<tr style='background-color: #ffff00'>\n";
        echo "<td valign='top'>";
        for ($i = 0; $i < count($masterData[$value]["seller"]); $i++) {
                echo $masterData[$value]["seller"][$i];
                echo "<br>";
        }
        echo "</td>";
        echo "<td></td>";
        echo "<td>" . $value . "</td>";
        echo "<td>N/A</td>";
        echo "<td>" . $masterData[$value]["closeDate"] . "</td>";
        echo "<td><input type=\"text\" class=\"cell editable\" name=\"index[]\" value=\"" . $masterData[$value]["pid"] . "\" data-row=\"" . $rowNum . "\" data-column=\"pid\" data-edited=\"false\" data-master=\"" . $value . "\" data-ticket=\"" . $value . "\" style=\"background-color: #ffff00; \">\n</td>";
        echo "<td>" . $masterData[$value]["lastName"] . ", " . $masterData[$value]["firstName"] . "</td>";
        echo "<td valign='top'>";
        $stid4 = oci_parse($connection, "SELECT INDEX__B1, INDEX__B2, INDEX__B3, INDEX__B4, INDEX__B5, INDEX__B6 FROM footprints.master3 WHERE MRID=" . $value);
        oci_execute($stid4);
        $indexNumbers = oci_fetch_array($stid4, OCI_ASSOC+OCI_RETURN_NULLS);
        $idx1 = $indexNumbers["INDEX__B1"];
        $idx2 = $indexNumbers["INDEX__B2"];
        $idx3 = $indexNumbers["INDEX__B3"];
        $idx4 = $indexNumbers["INDEX__B4"];
        $idx5 = $indexNumbers["INDEX__B5"];
        $idx6 = $indexNumbers["INDEX__B6"];
        
        for ($i = 0; $i < count($masterData[$value]["index"]); $i++) {
            switch ($masterData[$value]["index"][$i]) {
                case $idx1:
                    $theIndex = 1;
                    break;
                case $idx2:
                    $theIndex = 2;
                    break;
                case $idx3:
                    $theIndex = 3;
                    break;
                case $idx4:
                    $theIndex = 4;
                    break;
                case $idx5:
                    $theIndex = 5;
                    break;
                case $idx6:
                    $theIndex = 6;
                    break;
                default: 
                    $theIndex = 1;
            }
            echo "<input type=\"text\" class=\"multicell editable\" name=\"index[]\" value=\"" . $masterData[$value]["index"][$i] . "\" data-row=\"" . $rowNum . "\" data-column=\"index\" data-edited=\"false\" data-master=\"" . $value . "\" data-index=\"" . $theIndex . "\" data-ticket=\"" . $value . "\" style=\"background-color: #ffff00; \">\n";
            echo "<br>";
        }
        echo "</td>";
        echo "<td valign='top'>";
        for ($i = 0; $i < count($masterData[$value]["indexPct"]); $i++) {
                echo $masterData[$value]["indexPct"][$i];
                echo "<br>";
        }
        echo "</td>";
        echo "<td></td>";
        echo "<td valign='top'>";
        for ($i = 0; $i < count($masterData[$value]["qty"]); $i++) {
                echo $masterData[$value]["qty"][$i];
                echo "<br>";
        }
        echo "</td>";
        echo "<td valign='top'>";
        for ($i = 0; $i < count($masterData[$value]["rate"]); $i++) {
                echo $masterData[$value]["rate"][$i];
                echo "<br>";
        }
        echo "</td>";
        echo "<td valign='top'>";
        for ($i = 0; $i < count($masterData[$value]["cost"]); $i++) {
                echo $masterData[$value]["cost"][$i];
                echo "<br>";
        }
        echo "</td>";
        echo "<td>" . $masterData[$value]["billable"] . "</td>";
        echo "</tr>";

        // Begin Children Tickets
        for ($i = 0; $i < count($masterArray[$value]); $i++) {
            $rowNum++;
            $currentTicket = $masterArray[$value][$i];
            echo "<tr>\n";
            echo "<td valign='top'>";
            for($j = 0; $j < count($ticketData[$currentTicket]["seller"]); $j++) {
                echo $ticketData[$currentTicket]["seller"][$j];
                echo "<br>";
            }
            echo "</td>";
            echo "<td valign='top'>";
                foreach ($ticketData[$currentTicket]["seller"] as $s) {
                    for ($j = 0; $j < count($ticketData[$currentTicket][$s]["prodCode"]); $j++) {
                        echo $ticketData[$currentTicket][$s]["prodCode"][$j];
                        echo "<br>";
                    }
                }
            echo "</td>\n";
            echo "<td valign='top'>" . $currentTicket . "</td>\n";
            echo "<td valign='top'><ul>";
            foreach($ticketData[$currentTicket]["seller"] as $s) {
                foreach ($ticketData[$currentTicket][$s]["prodCode"] as $pc) {
                    for ($j = 0; $j < count($invalidReasons[$currentTicket][$s][$pc]); $j++) {
                        echo "<li>";
                        echo $invalidReasons[$currentTicket][$s][$pc][$j];
                        echo "</li>";
                    }
                }
            }
            echo "</td></ul>";
            echo "<td valign='top'><input type=\"text\" class=\"cell editable\" name=\"closeDate[]\" value=\"" . $ticketData[$currentTicket]["closeDate"] . "\" data-row=\"" . $rowNum . "\" data-column=\"closeDate\" data-edited=\"false\" data-master=\"" . $value . "\" data-ticket=\"" . $currentTicket . "\"></td>\n";
            echo "<td valign='top'><input type=\"text\" class=\"cell editable\" name=\"pid[]\" value=\"" . $ticketData[$currentTicket]["pid"] . "\" data-row=\"" . $rowNum . "\" data-column=\"pid\" data-edited=\"false\" data-master=\"" . $value . "\" data-ticket=\"" . $currentTicket . "\"></td>\n";
            echo "<td valign='top'><input type=\"text\" class=\"cell editable\" name=\"buyer[]\" value=\"" . $ticketData[$currentTicket]["buyer"] . "\" data-row=\"" . $rowNum . "\" data-column=\"buyer\" data-edited=\"false\" data-master=\"" . $value . "\" data-ticket=\"" . $currentTicket . "\"></td>\n";
            echo "<td valign='top'>";
            $stid4 = oci_parse($connection, "SELECT INDEX__B1, INDEX__B2, INDEX__B3, INDEX__B4, INDEX__B5, INDEX__B6 FROM footprints.master3 WHERE MRID=" . $currentTicket);
            oci_execute($stid4);
            $indexNumbers = oci_fetch_array($stid4, OCI_ASSOC+OCI_RETURN_NULLS);
            $idx1 = $indexNumbers["INDEX__B1"];
            $idx2 = $indexNumbers["INDEX__B2"];
            $idx3 = $indexNumbers["INDEX__B3"];
            $idx4 = $indexNumbers["INDEX__B4"];
            $idx5 = $indexNumbers["INDEX__B5"];
            $idx6 = $indexNumbers["INDEX__B6"];
            for ($j = 0; $j < count($ticketData[$currentTicket]["index"]); $j++) {
                switch ($ticketData[$currentTicket]["index"][$j]) {
                    case $idx1:
                        $theIndex = 1;
                        break;
                    case $idx2:
                        $theIndex = 2;
                        break;
                    case $idx3:
                        $theIndex = 3;
                        break;
                    case $idx4:
                        $theIndex = 4;
                        break;
                    case $idx5:
                        $theIndex = 5;
                        break;
                    case $idx6:
                        $theIndex = 6;
                        break;
                    default: 
                        $theIndex = 1;
                }
                echo "<input type=\"text\" class=\"multicell editable\" name=\"index[]\" value=\"" . $ticketData[$currentTicket]["index"][$j] . "\" data-row=\"" . $rowNum . "\" data-column=\"index\" data-edited=\"false\" data-master=\"" . $value . "\" data-index=\"" . $theIndex . "\" data-ticket=\"" . $currentTicket . "\">\n";
                echo "<br>";
            }
            echo "</td>";
            echo "<td valign='top'>";
            foreach ($ticketData[$currentTicket]["index"] as $idx) {
                echo $ticketData[$currentTicket][$idx]["indexPct"];
                echo "<br>";
            }
            echo "</td>";
            echo "<td valign='top'>";
            foreach ($ticketData[$currentTicket]["index"] as $idx) {
                echo $ticketData[$currentTicket][$idx]["rawQty"];
                echo "<br>";
            }
            echo "</td>";
            echo "<td valign='top'>";
            foreach ($ticketData[$currentTicket]["index"] as $idx) {
                echo $ticketData[$currentTicket][$idx]["qty"];
                echo "<br>";
            }
            echo "</td>";
            echo "<td valign='top'>" . $ticketData[$currentTicket]["rate"] . "</td>\n";
            echo "<td valign='top'>";
            foreach ($ticketData[$currentTicket]["index"] as $idx) {
                echo $ticketData[$currentTicket][$idx]["cost"];
                echo "<br>";
            }
            echo "</td>";
            echo "<td valign='top'><select class=\"cell editable\" name=\"billable[]\" value=\"" . $ticketData[$currentTicket]["billable"] . "\" data-row=\"" . $rowNum . "\" data-column=\"billable\" data-edited=\"false\" data-master=\"" . $value . "\" data-ticket=\"" . $currentTicket . "\"><option value='Yes'>Yes</option><option value='No'>No</option></select></td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo "</div><br>";
        $number += 1;
        $rowNum++; 
    }
        echo "TOTAL ROWS: " . $rowNum; 
?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    <!-- Tablesorter JavaScript -->
    <script src="../js/jquery.tablesorter.js"></script>
    <script src="../js/jquery.tablesorter.widgets.js"></script>
    <script src="../js/jquery.tablesorter.pager.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
    <script src="../js/bounce.js"></script>
    <script src="../js/financial.js"></script>
    <!-- Table To JSON jQuery -->
    <script src="../js/jquery.tabletojson.min.js"></script>
    <script src="../js/d3.min.js"></script>
    <script src="../js/c3.min.js"></script>
    <!--<script src="../dist/js/revenue-time.js"></script>-->
    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
                responsive: true
        });
    });
    </script>

</body>

</html>
