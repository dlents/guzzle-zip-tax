<?php
/**
 * Created by David Lents <david@lents.net>
 * Date: 2013-07-31
 * Time: 11:58
 * Created with JetBrains PhpStorm
 */

require_once 'vendor/autoload.php';


$api_key = '64XXXXX'; // Testing

$type = 'JSON';

// Optional arguments

$opts = array(
    'city' => 'Healdsburg'
);

$zipTax = new GuzzleZipTax($api_key, $type);
$zipData = $zipTax->fetch('95448', $opts);
print_r($zipData);

if ($type === 'XML') {
    print('Tax Rate: ' . $zipData[0]->taxSales . "\n");
    print('Rounded Tax Rate: ' . roundTaxRate($zipData[0]->taxSales) . "\n");
}
else {
    print('Tax Rate: ' . $zipData[0]['taxSales'] . "\n");
    print('Rounded Tax Rate: ' . roundTaxRate($zipData[0]['taxSales']) . "\n");
}

$testAmt = '0.082550003278255';
print "Rounding test ($testAmt) = " . roundTaxRate($testAmt) . "\n";

function roundTaxRate($rate) {
    return sprintf('%.3f', round(floatval($rate) * 100, 3));
}
