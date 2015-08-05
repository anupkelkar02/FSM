<?php
 $Url  = 'Https://Www.Google.Com/accounts/ClientLogin' ;
  $Fields  = array (
    'Email'  => 'fingerskies@gmail.com  ' ,
    'Passwd'  => '9428803041p' ,
    'AccountType'  => 'HOSTED_OR_GOOGLE' ,
    'Service'  => 'wise' ,
    'Source'  => 'PFBC'
  );
 
  $this->$Curl  = curl_init ();
  curl_setopt ( $curl , CURLOPT_URL, $url );
  curl_setopt ( $curl , CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ( $curl , CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ( $curl , CURLOPT_POST, true);
  curl_setopt ( $curl , CURLOPT_POSTFIELDS, $fields );
  $Response  = curl_exec ( $curl );
  $Status  = curl_getinfo ( $curl , CURLINFO_HTTP_CODE);
  curl_close ( $curl );
 
  if  ( $status  == 200) {
    echo  'auth status IS 200'  . PHP_EOL;
    // After successful authentication, it will save the authentication token
    if(stripos($response ,'auth =' )!= false) {
      preg_match ( "/ auth = ([A-z0-9 _ \ -] +) / I" , $response , $matches );
      $Token  = $matches [1];
    }
  }
  ?>