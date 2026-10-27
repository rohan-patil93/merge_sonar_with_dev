<?php
$file = "xml/cleaned_item_master.xml"; // items
$xmlObject = simplexml_load_file($file);

$json = json_encode($xmlObject);
$phpArray = json_decode($json, true); // <-- this gives you the PHP array

echo "<pre>"; print_r($phpArray['BODY']['IMPORTDATA']['REQUESTDATA']['TALLYMESSAGE']); echo "</pre>";

/**/
