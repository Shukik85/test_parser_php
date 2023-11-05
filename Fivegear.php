<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/simple_html_dom.php");

class Fivegear
{
    private $resource = "https://пятаяпередача.рф/manufacturers";


    public function get($brand = "")
    {
        $result = [];
        $brand_page = NULL;
        $manufacturers = [];

        if($brand != "")
        {
            $manufacturers = $this->getDomObjByUrl($this->resource)->find(".mfr-link-cell");
        }

        if(count($manufacturers) > 0)
        {
            foreach ($manufacturers as $key => $manufacturer) {
                $as = $manufacturer->find('a');
                if(count($as) > 0)
                {
                    foreach ($as as $a) {
                        if($a->plaintext == $brand){
                            $brand_page = $this->getDomObjByUrl("https://". parse_url($this->resource)["host"] . $a->href);
                            break;
                        }
                    }
                    if($brand_page)
                    {
                        $result["status"] = "200";
                        $result["result"] = array();
                        $result["result"]["id"] = $key + 1;
                        $result["result"]["brand"] = $a->plaintext;
                        $result["result"] = array_merge($result["result"], $this->parseBrand($brand_page));
                        break;
                    }else{
                        $result["status"] = "204";
                        $result["result"] = "No Content";
                    }
                }else{
                    $result["status"] = "204";
                    $result["result"] = "1 No Content";
                }
            }
        }else{
            $result["status"] = "204";
            $result["result"] = "2 No Content";
        }

        return $result;
    }

    private function parseBrand($brand_page)
    {
        
        $result = [
            "description"   => $brand_page->find(".manufacturer-info-description p", 0)->plaintext,
            "brand_logo"    => "https://". parse_url($this->resource)["host"] . $brand_page->find(".manufacturer-logo-img", 0)->src,
            "brand_sample"  => "https://". parse_url($this->resource)["host"] . $brand_page->find(".img-fluid", 0)->src,
            "info"          =>  [
            ],
        ];
        
        $info_table_rows = $brand_page->find(".mfr-property-row");
        
        foreach ($info_table_rows as $row) {
            $as = $row->find(".mfr-prop-value a");
            if(count($as) > 0)
            {
                $result["info"][$row->find(".mfr-prop-name", 0)->plaintext] = [];
                foreach ($as as $a) {
                    array_push($result["info"][$row->find(".mfr-prop-name", 0)->plaintext], $a->href);
                }
            }else{
                $result["info"][$row->find(".mfr-prop-name", 0)->plaintext] = $row->find(".mfr-prop-value", 0)->plaintext;
            }
        }
        return $result;
    }

    private function getDomObjByUrl($url)
    {
        $dom = new simple_html_dom;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
        $html = curl_exec($curl);
    
        if (curl_error($curl))
            die(curl_error($curl));
    
        curl_close($curl);

        $dom->load($html);

        return $dom;
    }
}