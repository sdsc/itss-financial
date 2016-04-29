<?php
	set_time_limit(0);
	require 'config.php';
	$projfield = array();
	$abfield = array();
	$changedStatus;

	// $_POST is receiving a JSON string when the user clicks "Apply Changes." The JSON includes all
	// edited tickets with their edited field values.
	foreach($_POST as $ticket=>$infoArray) {
		foreach($infoArray as $property=>$value) {
			if (isAbField($property)) {
				$abfield[$property] = $value;
			} else if ($property == "status") {
				$changedStatus = $value;
			} else {
				$projfield[$property] = $value;
			}
				
		}
		try {
			$client = new SoapClient(NULL,
   				array(
					"location"=>"http://footprints.sdsc.edu/MRcgi/MRWebServices.pl",
					"uri"=>"MRWebServices",
					"style"=>SOAP_RPC,
        			"use" => SOAP_ENCODED       
   				)
			);
			
			if (count($projfield) == 0 && count($abfield) == 0) {
				if ($changedStatus == "Open" || $changedStatus == "Closed") {
					$issue_number = $client->MRWebServices__editIssue($username, $footprintPW,'',
					array(
						"projectID" =>3,
						"mrID" => $ticket,
	    				"status" => $changedStatus
	            	)
					);
					$changedStatus = "";
					echo "Issue successfully changed.";
				} else {
					echo "No changes were made.";
				}
			} else if (count($projfield) == 0) {
				if ($changedStatus == "Open" || $changedStatus == "Closed") {
					$issue_number = $client->MRWebServices__editIssue($username, $footprintPW,'',
					array(
						"projectID" =>3,
						"mrID" => $ticket,
	    				"status" => $changedStatus,
	    				"abfields" => $abfield
	            	)
					);
					$changedStatus = "";
					echo "Issue successfully changed.";
				} else {
					$issue_number = $client->MRWebServices__editIssue($username, $footprintPW,'',
						array(
						"projectID" =>3,
						"mrID" => $ticket,
	    				"abfields" => $abfield
	            	)
					);
				}
				echo "Issue successfully changed.";
			} else if (count($abfield) == 0) {
				if ($changedStatus == "Open" || $changedStatus == "Closed") {
					$issue_number = $client->MRWebServices__editIssue($username, $footprintPW,'',
					array(
						"projectID" =>3,
						"mrID" => $ticket,
	    				"status" => $changedStatus,
	    				"projfields" => $projfield
	            	)
					);
					$changedStatus = "";
					echo "Issue successfully changed.";
				} else {
					$issue_number = $client->MRWebServices__editIssue($username, $footprintPW,'',
					array(
						"projectID" =>3,
						"mrID" => $ticket,
    					"projfields" => $projfield
            		)
					);
				}
				echo "Issue successfully changed.";
			} else {
				$issue_number = $client->MRWebServices__editIssue($username, $footprintPW,'',
				array(
					"projectID" =>3,
					"mrID" => $ticket,
    				"projfields" => $projfield,
    				"abfields" => $abfield
            	)
				);
				echo "Issue successfully changed.";
			}
 
 		} catch (SoapFault $exception) {
			echo "ERROR! - Got a SOAP exception:\n";
			echo $exception;
           
 		}
	} 

	function isAbField($property) {
		if ($property == "First__bName" || $property == "Last__bName" || $property == "PID") { return true; }
		return false;
	}
?>


