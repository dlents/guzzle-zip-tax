
# GuzzleZipTax
### A PHP wrapper for the zip-tax.com API using Guzzle


[Zip-Tax.com](http://www.zip-tax.com) provides a very simple API to look up sales tax information for a given US zip code.
See the [Zip-Tax documentation](http://www.zip-tax.com/documentation) for API details.

A large project I work on recently decided to start using the [Zip-Tax.com](http://www.zip-tax.com) service instead of keeping our own zip database updated. At the time of this writing, there is at least one other functionally equivalent project on GitHub, but its (viral) license was not compatible with our project, so I didn't really look much farther than that. I'm sure it works fine, but because of the licensing issue and my preference for the excellent [Guzzle HTTP client](http://guzzlephp.org/), I spent just a few minutes cooking this up and gave it a non-viral (BSD) license. I hope others may find it useful, but it serves our project's needs nicely, so it has already fulfilled its primary purpose.

    <?php

    require_once 'vendor/autoload.php';

    $api_key = '64XXXXX'; // Obtain a real API key from @link http://www.zip-tax.com/

    // New class object
    $zipTax = new GuzzleZipTax($api_key);

    // The constructor allows an optional format argument (defaults to 'JSON').
    // You may specify 'XML' if you prefer SimpleXMLElement responses:
    // $zipTax = new GuzzleZipTax($api_key, 'XML');

    // Optional argument to the fetch() method is an array which may contain:
    // 'city' (full city name), 'state' (2-letter state code)
    $opts = array(
        'city' =>  'Healdsburg',
        'state' => 'CA'
    );

    // NOTE: Specifying the city is redundant unless the Zip Code you're looking up has more than one city
    //   +   E.g. $zipTax->fetch('95448') returns 5 results, whereas $zipTax->fetch('95448', $opts) returns just the one you want.

    $zipData = $zipTax->fetch('95448', $opts);
    // print_r($zipData);

