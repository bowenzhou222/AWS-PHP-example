<?php
$url = "https://www.amazon.com/gp/pdp/profile/A65ZZAYUKMWC1/ref=cm_cr_dp_pdp";

$webcontent = file_get_contents($url);

echo $webcontent;

?>