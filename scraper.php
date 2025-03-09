<?php

// include the Simple HTML DOM parser library -> download from source and paste its simple_html_dom.php file in your project folder
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

// create a new Simple HTML DOM instance
$html = str_get_html($htmlContent);

// obtain the product containers
$products = $html->find(".product");

// create an empty product array to collect the extracted data
$productData = array();

// loop through the product container to extract its elements
foreach ($products as $product) {

    // find the name elements within the current product element
    $name = $product->find(".woocommerce-loop-product__title", 0);

    // find the image elements within the current product element
    $image = $product->find("img", 0);

    // find the price elements within the current product element
    $price = $product->find("span.price", 0);

    // check if the target elements exist with the required attributes
    if (
        $name && $price && $image 
        && isset($name->plaintext)
        && isset($price->plaintext) 
        && isset($image->src)
        
    ) {

        // decode the price symbol to $
        $decodedPrice = html_entity_decode($price->plaintext);

        // create an array of the extracted data
        $productInfo = array(
            "Name" => $name->plaintext,
            "Price" => $decodedPrice,
            "Image URL" => $image->src
        );

        // append the extracted data to the empty product array
        $productData[] = $productInfo;
    }
}

// define the path to the CSV file
$csvFilePath = "products.csv";

// open the CSV file for writing
$file = fopen($csvFilePath, "w");

// write the header row to the CSV file
fputcsv($file, array_keys($productData[0]));

// write each product's data to the CSV file
foreach ($productData as $product) {
    fputcsv($file, $product);
}

// close the CSV file
fclose($file);

// output a message indicating successful CSV creation
echo "CSV file created successfully: $csvFilePath";

// clean up resources
$html->clear();
?>
