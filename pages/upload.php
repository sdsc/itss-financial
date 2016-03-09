<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$target_dir = __DIR__ .  "/uploads/";
$old_target_file = $target_dir . "old.txt";
$new_target_file = $target_dir . "new.txt";
$uploadOk = 1;
$oldFileType = pathinfo($old_target_file,PATHINFO_EXTENSION);
$newFileType = pathinfo($new_target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
if($_POST["version"] === "1") {
    echo "<ul>";
    if(!isset($_FILES["oldFileToUpload"])) {
        echo "<li>Error: You must upload an old report.</li>";
        $uploadOk = 0;
        return;
    }

    if(!isset($_FILES["newFileToUpload"])) {
        echo "<li>Error: You must upload a new report.</li>";
        $uploadOk = 0;
        return;
    }

    if($_FILES['oldFileToUpload']['type'] != 'text/plain') {
        echo "<li>Error: Old report is not a text file.</li>";
        $uploadOk = 0;
    }

    if($_FILES['newFileToUpload']['type'] != 'text/plain') {
        echo "<li>Error: New report is not a text file.</li>";
        $uploadOk = 0;
    }

    if($oldFileType != "txt") {
        echo "<li>Error: Old report is not a .txt file.</li>";
        $uploadOk = 0;
    }

    if($newFileType != "txt") {
        echo "<li>Error: New report is not a .txt file.</li></ul>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded. Please fix the above errors.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["oldFileToUpload"]["tmp_name"], $old_target_file) &&
            move_uploaded_file($_FILES["newFileToUpload"]["tmp_name"], $new_target_file)) {
            echo "The files have been uploaded.";
        } else {
            echo "The files have not been uploaded.";
        }
    }
} else if ($_POST["version"] === "2") {
    echo "<ul>";
    if(!isset($_FILES["newFileToUpload"])) {
        echo "<li>Error: You must upload a new report.</li>";
        $uploadOk = 0;
        return;
    }

    if($_FILES['newFileToUpload']['type'] != 'text/plain') {
        echo "<li>Error: New report is not a text file.</li>";
        $uploadOk = 0;
    }

    if($newFileType != "txt") {
        echo "<li>Error: New report is not a .txt file.</li></ul>";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($new_target_file)) { // only one file... replace old.txt
        rename($target_dir . "new.txt", $target_dir . "old.txt");
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded. Please fix the above errors.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["newFileToUpload"]["tmp_name"], $new_target_file)) {
            echo "The file has been uploaded.";
        } else {
            echo "The file has not been uploaded.";
        }
    }
}

$old = file($target_dir . 'old.txt');
$new = file($target_dir . 'new.txt');

$oldArray = array();
$newArray = array();

$prev = 0;

foreach ($old as $line) {
    if ($line == $old[0]) {
        continue;
    }
    $array = explode("\t", $line);
    $ticketNum = $array[2];
    if ($ticketNum != $prev) {
        array_push($oldArray, $ticketNum);
        $prev = $ticketNum;
    }
}

$prev = 0;
foreach ($new as $line) {
    if ($line == $new[0]) {
        continue;
    }
    $array = explode("\t", $line);
    $ticketNum = $array[2];
    if ($ticketNum != $prev) {
        array_push($newArray, $ticketNum);
        $prev = $ticketNum;
    }
}

sort($oldArray, SORT_NUMERIC);
sort($newArray, SORT_NUMERIC);

$newfile = fopen($target_dir . "changes.txt", "w") or die("Unable to open file!");

$i = 0;
$j = 0;

while ($i < count($oldArray) && $j < count($newArray)) {
    //echo "Comparing index i " . $i . " " . $oldArray[$i] . " with index j " . $j . " " . $newArray[$j] . "\n";
    if(intval($oldArray[$i]) == intval($newArray[$j])) {
        //echo "Equal\n";
        $i++;
        $j++;
    } else if (intval($oldArray[$i]) < intval($newArray[$j])) {
        //echo "Less than\n";
        $i++;
    } else if (intval($oldArray[$i] > $newArray[$j])) {
        //echo "Greater than\n";
        fwrite($newfile, $newArray[$j] . "\n");
        $j++;
    }
}

while ($j < count($newArray)) {
    fwrite($newfile, $newArray[$j] . "\n");
    $j++;
}

fclose($newfile);
echo "done";
?>