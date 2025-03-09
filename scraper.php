<?php

// include the Simple HTML DOM parser library
include_once("simple_html_dom.php");

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

// ignore SSL verification (not recommended in production)
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

// close cURL session
curl_close($curl);

// create a new Simple HTML DOM instance and parse the HTML
$html = str_get_html($htmlContent);

// find the first product's name
$name = $html->find(".woocommerce-loop-product__title", 0);

// find the first product image
$image = $html->find("img", 0);

// find the first product's price
$price = $html->find("span.price", 0);

// decode the HTML entity in the currency symbol
$decodedPrice = html_entity_decode($price->plaintext);

// print the extracted data
echo "Name: $name->plaintext \n";
echo "Price: $decodedPrice \n";
echo "Image URL: $image->src \n";

// clean up resources
$html->clear();
?>
