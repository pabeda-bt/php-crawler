<?php namespace Pabeda\Crawler;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Crawler {
    private $crawledUrls=[];
    private $errorUrls=[];
    private $client;
    private $maxRequestcount=0;
    private $requestCount=0;
    /**
     * crawl given url, get internal url's and crawl them again
     *
     * @param string $url
     */

    function __construct(){
        $this->client=new Client([
            'allow_redirects' => true,
            'cookies' => true,
        ]);
    }

    function crawl($url,$func){
        $md5Url=md5($url);
        if (in_array($md5Url,$this->crawledUrls)){
            return;
        }
        try {
            $this->crawledUrls[]=$md5Url;
            $response = $this->client->request("GET", $url);
            $html=$response->getBody()->getContents();

            $this->requestCount++;
            echo $this->requestCount."\n";

            unset($response);

            if ($func) $func($url,$html);

            $domCrawler=new DomCrawler($html);
            unset($html);
            $urlsToCrawl=array_unique($domCrawler->filterXpath('//a')->extract(['href']));
            unset($domCrawler);

            foreach ($urlsToCrawl as $urlToCrawl) {
                if ($this->maxRequestcount!=0 && $this->requestCount>=$this->maxRequestcount) return;
                if ($this->isCrawlable($url, $urlToCrawl)) {
                    $this->crawl($this->normalizeUrl($url, $urlToCrawl), $func);
                }
            }
        } catch(\Exception $e){
            $this->errorUrls[]=$url;
        }
    }

    function isCrawlable($sampleUrl,$url) {
        $sampleData=parse_url($sampleUrl);
        $urlData=parse_url($url);

        if (isset($urlData['scheme']) && $urlData['scheme']==='mailto'){
            return false;
        }

        return (isset($urlData['host']))?($sampleData['host']===$urlData['host']):true;

    }

    function normalizeUrl($sampleUrl,$url) {
        $sampleData=parse_url($sampleUrl);
        $urlData=parse_url($url);

        $urlData['scheme']=(isset($urlData['scheme']))?$urlData['scheme']:$sampleData['scheme'];
        $urlData['query']=(isset($urlData['query']))?"?".$urlData['query']:"";
        $urlData['path']=(isset($urlData['path']))?$urlData['path']:"";
        //$urlData['fragment']=(isset($urlData['fragment']))?"/#".$urlData['fragment']:"";

        if (!isset($urlData['host'])) {
            $urlData['host']=$sampleData['host'];
            //if (!$this->isPathAbsolute($urlData['path'])) {
                //$urlData['path']=$sampleData['path'].$urlData['path'];
                $urlData['path']="/".$urlData['path'];
            //}
        }

        return $urlData['scheme']."://".$urlData['host'].$urlData['path'].$urlData['query'];
    }

    function isPathAbsolute($path){
        return strpos($path,'/')===0;
    }
}