<?php
/**
 * Created by David Lents <david@lents.net>
 * Date: 2013-07-31
 * Time: 11:58
 * Created with JetBrains PhpStorm
 */

require_once 'vendor/autoload.php';


$api_key = '64XXXXX'; // Real API key from zip-tax.com required

// the default is 'JSON'
$type = 'XML';

// Optional arguments 'city', 'state'
$opts = array(
    'city' => 'Healdsburg'
);

$zipTax = new GuzzleZipTax($api_key, $type);
$zipData = $zipTax->fetch('95448', $opts);
// print_r($zipData);

// Sales tax percent
if ($type === 'XML') {
    printf("Tax Rate: %.3f%%\n", floatval($zipData[0]->taxSales) * 100);
}
else {
    printf("Tax Rate: %.3f%%\n", floatval($zipData[0]['taxSales']) * 100);
}
