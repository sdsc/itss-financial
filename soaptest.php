<?php       
set_time_limit(0);
    echo "Hello";
    try {
            $client = new SoapClient(NULL,
                array(
                    "location"=>"http://footprints.sdsc.edu/MRcgi/MRWebServices.pl",
                    "uri"=>"MRWebServices",
                    "style"=>SOAP_RPC,
                    "use" => SOAP_ENCODED       
                )
            );
            echo "NOWWWWWWWW";
            $issue_number = $client->MRWebServices__editIssue("kdly","@daQh2obottle",'',
                array(
                    "projectID" =>3,
                    "mrID" =>61141,
                    "status" => "Closed"
                    )
            );
        print "<BR><b> Issue changed;<hr>\n";
 
        } catch (SoapFault $exception) {
            echo "ERROR! - Got a SOAP exception:\n";
            echo $exception;
           
        }
?>