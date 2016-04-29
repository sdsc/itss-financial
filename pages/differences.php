<?php
	require_once 'class.Diff.php';
	$target_dir = __DIR__ . "/uploads/";
	$diff = Diff::compareFiles('old.txt', 'new.txt');
	$newfile = fopen($target_dir . "changes.txt", "w") or die("Unable to open file!");
	foreach ($diff as $line) {
		if ($line[1] === 2) {
			$array = explode("\t", $line[0]);
			fwrite($newfile, $array[2] . "\n");
		}
	}
	fclose($newfile);
	echo "done";
?>
