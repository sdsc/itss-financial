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
        <?php require 'sidebar.php' ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Recharge Statements
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
                            <div class="pager" id="tablePager">
                                <button type="button" class="btn btn-default btn-xs">
                                    <span class="glyphicon glyphicon-step-backward first" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="btn btn-default btn-xs">
                                    <span class="glyphicon glyphicon-triangle-left prev" aria-hidden="true"></span>
                                </button>
                                <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
                                <button type="button" class="btn btn-default btn-xs">
                                    <span class="glyphicon glyphicon-triangle-right next" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="btn btn-default btn-xs">
                                    <span class="glyphicon glyphicon-step-forward last" aria-hidden="true"></span>
                                </button>
                                <br>Page: <select class="gotoPage"></select>
                                Show: 
                                <select class="pagesize">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                </select>
                            </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped tablesorter" id="rechargeTable">
                                           <thead>
                                              <tr><th>Ledger Date</th>
                                                  <th>Sale Date</th>
                                                  <th>Index</th>
                                                  <th class="filter-select filter-onlyAvail" align="left">Seller</th>
                                                  <th align="left">Buyer</th>
                                                  <th class="filter-select filter-onlyAvail" align="left">Recharge Type</th>
                                                  <th align="left">Description</th>
                                                  <th align="right">Quantity</th>
                                                  <th align="right">Unit Cost</th>
                                                  <th align="right">Cost</th>
                                              </tr>
                                          </thead>
                                        <tbody>
                                            <?php
                                                require '../config.php';
                                                $connection = oci_connect($username, $password, $ezstring);
                                                $stid = oci_parse($connection, "SELECT * FROM recharge_pro.statements_v WHERE LEDGER_DT=201509 AND 
                                                    (SELLER='SRF PROJECT' OR SELLER='DESKTOP SYSTEMS' OR SELLER='ITRF DESKTOP' OR 
                                                        SELLER='SDSC WINDOWS/VM' OR SELLER='SRF CLOUD' OR SELLER='COLO MACHINE ROOM' 
                                                        OR SELLER='SDSC UNIX PLATFORM' OR SELLER='ITRF CONSULTING' OR SELLER='NETWORKING' 
                                                        OR SELLER='ITRF - SERVERS & CON' OR SELLER='ITRF SUPPORT' OR 
                                                        SELLER='ITRF - SUPPORT SERVI' OR SELLER='SDSC COMMVAULT')");
                                                oci_execute($stid);
                                                while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                                                    echo "<tr valign='top'>\n";
                                                    echo "    <td align='center'>" . ($row["LEDGER_DT"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["SALE_DT"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["ACCOUNT_INDEX"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["SELLER"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["BUYER"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["PRODUCT_NUMBER"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["DESCRIPTION"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["QUANTITY"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["UNIT_COST"]) . "</td>\n";
                                                    echo "    <td align='center'>" . ($row["TOTAL_COST"]) . "</td>\n";
                                                    echo "</tr>\n";
                                                }
                                            ?>
                                            
                                        </tbody>
                                    </table>
                                    <div id="filteredRowCount"></div>
                                    </div>
                            <!-- /.table-responsive -->
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

    <!-- Table To JSON jQuery -->
    <script src="../js/jquery.tabletojson.min.js"></script>
    <script src="../d3.min.js"></script>
    <script src="../c3.min.js"></script>
    <script src="../dist/js/revenue-time.js"></script>

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
