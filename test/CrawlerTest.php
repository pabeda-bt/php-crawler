<?php namespace Pabeda\Crawler\Test;



use Pabeda\Crawler\Crawler;

class CrawlerTest extends \PHPUnit_Framework_TestCase {

    public function testNormalizeUrl(){
        $crawler=New Crawler();

        $this->assertEquals("http://www.pabeda.com.tr/",$crawler->normalizeUrl("http://www.pabeda.com.tr/",""));
        $this->assertEquals("http://www.pabeda.com.tr/en",$crawler->normalizeUrl("http://www.pabeda.com.tr/","en"));
        $this->assertEquals("http://www.pabeda.com.tr/en/content",$crawler->normalizeUrl("http://www.pabeda.com.tr/","en/content"));
        $this->assertEquals("http://www.pabeda.com.tr/en",$crawler->normalizeUrl("http://www.pabeda.com.tr/","/en"));
        $this->assertEquals("http://www.pabeda.com.tr/en",$crawler->normalizeUrl("http://www.pabeda.com.tr/","http://www.pabeda.com.tr/en"));
        $this->assertEquals("http://www.pabeda.com.tr/en/",$crawler->normalizeUrl("http://www.pabeda.com.tr/","http://www.pabeda.com.tr/en/"));
        $this->assertEquals("http://www.pabeda.com.tr/en/?hello=world",$crawler->normalizeUrl("http://www.pabeda.com.tr/","http://www.pabeda.com.tr/en/?hello=world"));
        $this->assertEquals("http://www.pabeda.com.tr/en?hello=world",$crawler->normalizeUrl("http://www.pabeda.com.tr/","http://www.pabeda.com.tr/en?hello=world"));
        $this->assertEquals("http://shop.mango.com/TR/m/violeta/yeni",$crawler->normalizeUrl("http://shop.mango.com/TR/asc/violeta/yeni","TR/m/violeta/yeni"));
    }

    public function testIsCrawlable(){
        $crawler=New Crawler();

        $this->assertTrue($crawler->isCrawlable("http://www.pabeda.com.tr/","http://www.pabeda.com.tr/en"));
        $this->assertTrue($crawler->isCrawlable("http://www.pabeda.com.tr","http://www.pabeda.com.tr/en"));
        $this->assertTrue($crawler->isCrawlable("http://www.pabeda.com.tr/","/en"));
        $this->assertTrue($crawler->isCrawlable("http://www.pabeda.com.tr/","en"));
        $this->assertTrue($crawler->isCrawlable("http://www.pabeda.com.tr","en"));
    }


}