<?php
    require '../config.php';
    if (!$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') die('Invalid request');
    $connection = oci_connect($username, $password, $ezstring);
    $stid = oci_parse($connection, "SELECT * FROM recharge_pro.statements_v WHERE LEDGER_DT=201509");
    oci_execute($stid);
    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<tr valign='top'>\n";
        echo "    <td align='center'>" . ($row["LEDGER_DT"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["SALE_DT"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["ACCOUNT_INDEX"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["BUYER"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["PRODUCT_NUMBER"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["DESCRIPTION"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["QUANTITY"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["UNIT_COST"]) . "</td>\n";
        echo "    <td align='center'>" . ($row["TOTAL_COST"]) . "</td>\n";
        echo "</tr>\n";
    }
    echo "</tbody>";
?>