<?php

// specify the target website's URL
$url = "https://scrapingcourse.com/ecommerce/";

// initialize a cURL session
$curl = curl_init();

// set the website URL
curl_setopt($curl, CURLOPT_URL, $url);

// return the response as a string
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// follow redirects
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 

// ignore SSL verification
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// execute the cURL session
$htmlContent = curl_exec($curl);

// check for errors
if ($htmlContent === false) {

    // handle the error
    $error = curl_error($curl);
    echo "cURL error: " . $error;
    exit;
}

// print the HTML content
echo $htmlContent;

// close cURL session
curl_close($curl);
?>
