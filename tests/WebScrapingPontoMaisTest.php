<?php
namespace Token;

final class WebScrapingPontoMaisTest extends \PHPUnit\Extensions\Selenium2TestCase {
        
    protected function setUp(): void
    {
        //$this->setBrowser('chrome');
        $this->setupSpecificBrowser(array(
            'host' => '127.0.0.1',
            'port' => 4444,
            'browserName' => 'chrome',
            'desiredCapabilities' => array(
                array('chromeOptions' => array(
                    'args' => array('no-sandbox')
                ))
            ),
            'seleniumServerRequestsTimeout' => '50',
        ));
        $this->setBrowserUrl('https://app.pontomaisweb.com.br/');
    }

    public function testTitle()
    {
        var_dump($this->title());

        $this->assertEquals('Pontomais', $this->title());
    }
    
}