<?php

// include the Simple HTML DOM parser library
include_once("simple_html_dom.php");

// specify the target website's URL
$url = "https://scrapingcourse.com/ecommerce/";

// initialize an array to store all product data
$productData = array();

// create a function to scrape product data from a given URL
function scraper($url) {

    // log the currently scraped page
    echo "Scraping page: $url\n";

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
                "URL" => $name->plaintext,
                "price" => $decodedPrice,
                "image_src" => $image->src
            );

            // append the extracted data to the product array
            global $productData;
            $productData[] = $productInfo;
        }
    }

    // check if there is a next page
    $nextPageLink = $html->find("a.next", 0);
    if ($nextPageLink) {
        $nextPageUrl = $nextPageLink->href;

        // scrape data from the next page
        scraper($nextPageUrl);
    }
}

// call the function to start scraping from the initial URL
scraper($url);

// print the extracted products
print_r($productData);
?>
