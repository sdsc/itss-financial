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

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../css/upload.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">
    	<?php 
    		require "sidebar.php"; 
    	?>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Update Bounce Report</h1>
                        <h3>Compare Two Versions</h3>
                        <em>This will mark differences between the following two files uploaded.</em>
                        <br>
                        <label for="oldFileToUpload">Old Report: </label><input type="file" name="oldFileToUpload" id="oldFileToUpload1"><br>
                        <label for="newFileToUpload">New Report: </label><input type="file" name="newFileToUpload" id="newFileToUpload1"><br>
                        <br>
                        <button id="upload1">Upload</button>
                        <br>
                        <span id="loadingMessage1"><i class="fa fa-circle-o-notch fa-spin"></i>Generating differences... this could take a while.</span>
                        <span id="doneMessage1"></span> 
                        <hr>
                        <h3>Compare With Latest</h3>
                        <em>This will mark differences between the file uploaded and the last file uploaded.</em>
                        <br>
                        <label for="newFileToUpload">New Report: </label><input type="file" name="newFileToUpload" id="newFileToUpload2"><br>
                        <br>
                        <button id="upload2">Upload</button>
                        <br>
                        <span id="loadingMessage2"><i class="fa fa-circle-o-notch fa-spin"></i>Generating differences... this could take a while.</span>
                        <span id="doneMessage2"></span>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
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

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <script>
        $("#upload1").on('click', function() {
            var file_data1 = $("#oldFileToUpload1").prop('files')[0];
            var file_data2 = $("#newFileToUpload1").prop('files')[0];
            var form_data = new FormData();
            form_data.append('version', 1);
            form_data.append('oldFileToUpload', file_data1);
            form_data.append('newFileToUpload', file_data2);
            //$("#doneMessage1").hide();
            $("#loadingMessage1").show();
            $.ajax({
                type: 'post',
                url: 'upload.php',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                complete: function(err, data) {
                    if (err) {
                        console.log(err.responseText);
                    }
                    console.log(data);
                    $("#loadingMessage1").hide();
                    $("#doneMessage1").html(data);
                    //$("#doneMessage1").show();
                }
            });
        });

        $("#upload2").on('click', function() {
            var file_data1 = $("#newFileToUpload2").prop('files')[0];
            var form_data = new FormData();
            form_data.append('version', 2);
            form_data.append('newFileToUpload', file_data1);
            $("#doneMessage2").hide();
            $("#loadingMessage2").show();
            $.ajax({
                type: 'post',
                url: 'upload.php',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                complete: function(err, data) {
                    if (err) {
                        console.log(err.responseText);
                        $("#doneMessage2").html(err.responseText);
                    } else {
                        $("#doneMessage2").html(data);
                    }
                    $("#loadingMessage2").hide();
                    $("#doneMessage2").show();
                }
            });
        });
    </script>

</body>

</html>
