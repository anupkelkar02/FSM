<?php

//error_reporting(0);
$fieldname = '$t';
$ShowWorksheet = 'SOP';

require_once 'application/libraries/google-api-php-client/src/Google_Client.php';
require_once 'application/libraries/google-api-php-client/src/contrib/Google_DriveService.php';
try {
    $client = new Google_Client();
    // Get your credentials from the console
    $client->setClientId('562916899979-sqstcvrkb5ji1364at447l35sl5pqfa3.apps.googleusercontent.com');
    $client->setClientSecret('JT8lSp-62FHMjbXNqFp2Gy4V');
    $client->setRedirectUri('http://fingerskies.com/az-twilio/getSheetData.php');
    $client->setScopes(array('https://www.googleapis.com/auth/drive', 'https://spreadsheets.google.com/feeds', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));

    $service = new Google_DriveService($client);
    $authUrl = $client->createAuthUrl();

    $authCode = "";

   // Authenticate 
    $tokenObject = $client->authenticate($authCode);
    $client->setAccessToken($tokenObject);

   //echo $tokenObject . "<br />";

    $tokens = json_decode($tokenObject);
    $refresh_token = $tokens->refresh_token;
    $access_token = $tokens->access_token;

    echo "Access Token: " . $access_token . "<br />";
    echo "Refresh Token: " . $refresh_token . "<br />";

    /* $authUrl = $client->createAuthUrl();*/
   //    $TOKEN=$_SESSION['accessToken'] = $client->authenticate($_GET['code']);
    // $client->setAccessToken($TOKEN); 
    $pageToken = NULL;
    $parameters = array();
    if ($pageToken) {
        $parameters['pageToken'] = $pageToken;
    }
    $files = $service->files->listFiles($parameters);
    echo '<pre>';
    //print_r($files);

    $NumberOfSheets = 3;
    $SheetCount = 1;

    foreach ($files['items'] as $file) {
        $mimeType = $file['mimeType'];
        $displayName = $file['title'];
        //$methodName = strtolower($displayName);
        //$methodName = str_replace(chr(32),"",$methodName);
        $fileId = $file['id'];
        echo $displayName . "<br />";
        if ($mimeType == 'application/vnd.google-apps.spreadsheet' && $displayName == $ShowWorksheet) {
            //echo "(" . $mimeType . ") " . $displayName . " - " . $fileId . "<br />";
            $SpreadsheetURL = 'https://docs.google.com/spreadsheets/d/' . $fileId . '/export?gid=0&amp;format=csv';

            echo $SpreadsheetURL;
            //exit;
            
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $SpreadsheetURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data1 = curl_exec($ch);
            
            curl_close($ch);
            var_dump($data1);
            //$SpreadsheetResults = json_decode($SpreadsheetResults);

            if (!ini_set('default_socket_timeout', 15)) {
                echo "<!-- unable to change socket timeout -->";
            }
            $getdata=  file_get_contents($SpreadsheetURL);
            file_put_contents('testdata.csv', $getdata);
            if (($handle = fopen($SpreadsheetURL, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                   $spreadsheet_data[] = $data;
                   
                }
                //var_dump($spreadsheet_data); exit;
            } else {
                die("Problem reading csv");
                fclose($handle);
            }

            $Sheet_Count = 1;

            // Each Sheet
            //		foreach($SpreadsheetResults as $Sheet)
            //			{
            //			
            //			// BEGIN REST			
            //			
            //			// Set the URL Path
            //			$route = '/' . $methodName . '/';
            //			$app->get($route, function () use ($app,$fileId,$Sheet_Count,$ACCESSTOKEN,$fieldname){
            //							
            //				$SpreadsheetContentURL = 'https://spreadsheets.google.com/feeds/list/' . $fileId . '/' . $Sheet_Count . '/private/full?v=3.0&access_token=' . $ACCESSTOKEN . "&alt=json";		
            //				//echo '<a href="' . $SpreadsheetContentURL . '">' . $SpreadsheetContentURL . '</a><br />';		
            //				
            //				$SpreadsheetContent = file_get_contents($SpreadsheetContentURL);			
            //				$SpreadsheetContent = str_replace('gsx$','',$SpreadsheetContent);
            //				$SpreadsheetContent = json_decode($SpreadsheetContent);
            //				$SpreadsheetRows = $SpreadsheetContent->{'feed'}->{'entry'};
            //			
            //				$ReturnData = array();
            //				  
            //				foreach($SpreadsheetRows as $SpreadsheetRow) 
            //					{
            //
    //						// Build Product Array
            //						$P = array();											
            //						
            //						// For Each Spreadsheet Row in Service Worksheet
            //						$FieldCount = 1;
            //						foreach($SpreadsheetRow as $key => $value) 
            //							{
            //							
            //							// The first eight are default sheet, everything after are columns
            //							if($FieldCount > 8){		
            //					  			$P["$key"] = $SpreadsheetRow->$key->$fieldname;
            //								}
            //							$FieldCount++;
            //							}												
            //						
            //						array_push($ReturnData, $P);	
            //		
            //					}	
            //					
            //				// Return JSON
            //				$app->response()->header("Content-Type", "application/json");
            //				echo format_json(json_encode($ReturnData));	
            //			
            //			});
            //			
            //			
            //			// End REST
            //			$Sheet_Count++;
            //			}
            //		
            //		$SheetCount++;			
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>