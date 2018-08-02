<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://toecyd.local/api/v1/signup",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "\u0001{\n\t\"name\": \"testname1\",\n\t\"email\": \"testmail1@mail.com\",\n\t\"password\": \"123456\",\n\t\"password_confirmation\": \"123456\"\n}",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache",
    "Content-Type: application/json",
    "Postman-Token: a257a5c7-5fd2-44d9-911e-6c68cc8464c7",
    "X-Requested-With: XMLHttpRequest"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}