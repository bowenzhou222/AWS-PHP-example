

<script type="text/javascript" src="jquery-3.1.0.min.js"></script>
<script type="text/javascript">
	function evalReviews(){
		//document.writeln("hello");

		
		var href = $(".crIFrame > .crIframeReviewList > .small > b > a").get(0);
		//window.alert(href);
		//document.writeln(href); 
		//if($obj = $("body > div")){
		//	window.alert($obj.length);
		//}
		//$.each($("[href]"),function(i,n){
		//	document.writeln("hello");
		//})

		
	}

</script>


<?php
        //header('Content-Type: text/plain');
        include_once 'simple_html_dom.php';
        ini_set('max_execution_time', 2000000); //300 seconds = 5 minutes
        

        
        function getRivewsEachPage($reviewUrl, $ASIN,$file){
                $html = file_get_html($reviewUrl);
                $reviewList = $html->find('div[class=a-section review]');
                //echo count($reviewList);
                foreach ($reviewList as $review){
                        //echo $review.'<br>';
                        if(count($reviewDetail = $review->find('div[class=a-row]')) >= 2){
                                $reviewStar = $reviewDetail[0];
                                //echo $ASIN.'&nbsp&nbsp&nbsp&nbsp'.substr($reviewStar->find('a i span')[0]->plaintext, 0, 3).'&nbsp&nbsp&nbsp&nbsp';
                                
                                $reviewer = $reviewDetail[1];
                                $reviewerName = $reviewer->find('span a')[0]->plaintext;
                                
                                //echo $reviewerName.'&nbsp&nbsp&nbsp&nbsp';
                                
                                $reviewerUrl = $reviewer->find('span a')[0]->href;
                                $reviewerUrl = str_replace('/gp/pdp/profile/', '', $reviewerUrl);
                                $reviewerID = strstr($reviewerUrl, '/', true);
                                
                                //echo $reviewerID.'<br>';
                                
                                $singleReview = $ASIN."\t".substr($reviewStar->find('a i span')[0]->plaintext, 0, 3)."\t"
                                        .$reviewerID."\t".$reviewerName."\n";
                                
                                fwrite($file, $singleReview);
                                //echo $singleReview;
                                
                                
                        }
                        //->find('a i span')->plaintext
                }
        }
        
        function getRivews($item,$file){
                $ASIN = $item->ASIN;
                $url = $item->CustomerReviews->IFrameURL;
                //$webContent = file_get_contents($url);
                $html = file_get_html($url);
                
                if($href = $html->find('.crIFrame .crIframeReviewList .small b a')){
                        //echo count($href);
                        //echo $href[0]->href;
        
                        //first page of all the reviews
                        //$webContent = file_get_contents($href[0]->href);
                        $htmlOfReviews = file_get_html($href[0]->href);
                        $numberOfReviews = str_replace("See all ", "", $href[0]->plaintext);
                        $numberOfReviews = str_replace(" customer reviews...", "", $numberOfReviews);//total number of reviews
                        //echo $numberOfReviews.'<br>';
                        
                        $numberOfPages = ceil($numberOfReviews/10);//total number of pages of all the reviews
                        //echo $numberOfPages.'<br>';
                        for($i = 0; $i < $numberOfPages; $i++){
                        //for($i = 0; $i < 1; $i++){
                                $reviewUrl = $href[0]->href."&pageNumber=".($i+1);
                                getRivewsEachPage($reviewUrl, $ASIN,$file);
                                //echo $reviewUrl.'<br>';
                        }
                       
                }
                
        
        }
        
        function getItemsEachPage($xmlStr,$file) {
                $simpleXmlStr = simplexml_load_string($xmlStr);
                //print_r($simpleXmlStr);
                /*
                $simpleXmlStr = new SimpleXMLElement($xmlStr);
                $namespaces = $simpleXmlStr->getDocNamespaces();
                //echo $namespaces[""];
                $nameSpace = $namespaces[""];
                $simpleXmlStr->registerXPathNamespace("a", $nameSpace);
                */
                
                //$item = $simpleXmlStr->Items->Item[0];
                
                //$item = $simpleXmlStr->Items->Item[0];
                foreach ($simpleXmlStr->Items->Item as $item) {
                        //echo "\n".$item->ASIN."\n";
                //foreach ($simpleXmlStr->xpath("//Item") as $item) {
                       //print($item->ASIN);
                       //$link = "<script>window.open(\"".$item->CustomerReviews->IFrameURL."\")</script>";
                       
                       //echo $link;
                       getRivews($item,$file);
                }
                
                
                
                //echo '<script>evalReviews(\''.'\')</script>';
                
                
        }

        function convertXmlStr($simpleXmlStr){
                $namespaces = $simpleXmlStr->getDocNamespaces();
                //echo $namespaces[""];
                $nameSpace = $namespaces[""];
                $simpleXmlStr->registerXPathNamespace("a", $nameSpace);
                return $simpleXmlStr;
        }
        
        function getTotalPages($simpleXmlStr){
                $totalPages = 0;
                if($totalPages = $simpleXmlStr->Items->TotalPages){
                        //echo $totalPages;
                        if($totalPages > 5){
                                $totalPages = 5;
                        }
                }
                
                return $totalPages;
        }
        
        
        
        
        function printSearchResults($parsed_xml, $SearchIndex){
                print("<table>");
                $numOfItems = $parsed_xml->Items->TotalResults;
                if($numOfItems>0){
                        foreach($parsed_xml->Items->Item as $current){
                                print("<td><font size='-1'><b>".$current->ItemAttributes->Title."</b>");
                                if (isset($current->ItemAttributes->Title)) {
                                        print("<br>Title: ".$current->ItemAttributes->Title);
                                } elseif(isset($current->ItemAttributes->Author)) {
                                        print("<br>Author: ".$current->ItemAttributes->Author);
                                } elseif
                                (isset($current->Offers->Offer->Price->FormattedPrice)){
                                        print("<br>Price:
            ".$current->Offers->Offer->Price->FormattedPrice);
                                }else{
                                        print("<center>No matches found.</center>");
                                }
                        }
                }
        }

        
        
        
?>


