<?php
        include_once 'functions.php';
        
        // Your AWS Access Key ID, as taken from the AWS Your Account page
        $aws_access_key_id = "your_access_key";
        
        // Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page
        $aws_secret_key = "your_secret_key";
        
        // The region you are interested in
        $endpoint = "webservices.amazon.com";
        
        $uri = "/onca/xml";
        
        $params = array(
            "Service" => "AWSECommerceService",
            "Operation" => "ItemSearch",
            "AWSAccessKeyId" => "your_access_key",
            "AssociateTag" => "your_associate_tag",
            "SearchIndex" => "All",
            "Keywords" => "laptop",
            "ItemPage" => "1",
            "ResponseGroup" => "Images,ItemAttributes,Offers,Reviews",
            //Valid values include 'salesrank','pmrank','price','-price','relevancerank','reviewrank','reviewrank_authority'.
            //"Sort" => "-price"
        );
        
        // Set current timestamp if not set
        if (!isset($params["Timestamp"])) {
            $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
        }
        
        // Sort the parameters by key
        ksort($params);
        
        $pairs = array();
        
        foreach ($params as $key => $value) {
            array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
        }
        
        // Generate the canonical query
        $canonical_query_string = join("&", $pairs);
        
        // Generate the string to be signed
        $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;
        
        // Generate the signature required by the Product Advertising API
        $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));
        
        // Generate the signed URL
        
        $request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
        
        //echo $request_url;
        
        
        
        $xmlStr = file_get_contents($request_url);
        $simpleXmlStr = simplexml_load_string($xmlStr);
        //$simpleXmlStr->asXML("products.xml");
        $totalPages = getTotalPages($simpleXmlStr);
        $myfile = fopen("ratings.txt", "w") or die("Unable to open file!");
        for($i=0;$i<$totalPages;$i++){
                $params['ItemPage'] = $i+1;
                $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
                $pairs = array();
                foreach ($params as $key => $value) {
                        array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
                }
                $canonical_query_string = join("&", $pairs);
                
                // Generate the string to be signed
                $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;
                
                // Generate the signature required by the Product Advertising API
                $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));
                
                // Generate the signed URL
                
                $request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
                
                //echo $request_url.'<br>';
                
                $xmlStr = file_get_contents($request_url);
                //$simpleXmlStr = simplexml_load_string($xmlStr);
                getItemsEachPage($xmlStr,$myfile);
        }
        fclose($myfile);
        
        
        
        //getItemsEachPage($simpleXmlStr);
        //printSearchResults($simpleXmlStr, $params["SearchIndex"]);
        //review webpage
        //$url = "https://www.amazon.com/review/product/B00B588I46?SubscriptionId=AKIAJS5IDCYXBGM7ZJCQ&tag=bowenzhou222-20&linkCode=xm2&camp=2025&creative=386001&creativeASIN=B00B588I46";
        //$str = file_get_contents($url);
        //echo $str;

?>

<html>
<?php 
        ///echo $xmlStr;
?>
</html>