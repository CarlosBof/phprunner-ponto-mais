<?php

namespace Token;

use Dotenv\Dotenv;
use Exception;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Token\Logger\Logger;

class Runner {
    
    public function execute() {
        
        gc_enable();
        
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../config/");
        $dotenv->load();

        Logger::get()->logMsg("Ponto Mais - iniciando execucao");
        
        $counterTry = 0;
        do {
            $isSuccess = true;
            $counterTry++;

            try {
            $driver = ChromeDriver::start();
            $driver->get('https://app.pontomais.com.br/');
            sleep(5);
            } catch(\Exception $e) {
                
                Logger::get()->logMsg(__DIR__ . '/../config/pontomais.fw.png');

                $notification =
                    (new Notification())
                    ->setIcon(__DIR__ . '/../config/pontomais.fw.png')
                    ->setTitle('Falha com a versão do ChromeDriver')
                    ->setBody('Versão do chromedriver desatualizada (' . $e->getMessage() . ')');

                $notifier = NotifierFactory::create();
                $notifier->send($notification);

                throw new \Exception($e->getMessage());
            }
            
            $loginInput = $driver->findElement(WebDriverBy::XPath("//*[@placeholder='Nome de usuário / cpf / e-mail']"));
            $loginInput->sendKeys($_ENV["PM_USER"]);
            
            $passwordInput = $driver->findElement(WebDriverBy::XPath("//*[@type='password']"));
            $passwordInput->sendKeys($_ENV["PM_PASS"]);
            
            Logger::get()->logMsg("Ponto Mais - Realizando o Login");
            $buttonLogin = $driver->findElement(WebDriverBy::XPath("//button[contains(@class,'pm-button') and contains(@class,'pm-primary')]/span/span[contains(text(),'Entrar')]"));
            $buttonLogin->click();
            sleep(5);

            Logger::get()->logMsg("Ponto Mais - Navegando para bater o ponto");
            $driver->navigate()->to("https://app2.pontomais.com.br/registrar-ponto");
            sleep(5);
            
            $lButtonPonto = $driver->findElements(WebDriverBy::XPath("//button[contains(@class,'pm-button') and contains(@class,'pm-primary')]"));
            Logger::get()->logMsg("Ponto Mais - Total de elementos(" . count($lButtonPonto) . ")");

            foreach($lButtonPonto as $buttonPonto) {
                if($buttonPonto->isDisplayed()) {
                    Logger::get()->logMsg("Ponto Mais - Batendo o Ponto");        
                    $buttonPonto->click();
                }
            }
            sleep(25);
            
            $driver->quit();

        } while($isSuccess === false AND $counterTry < 3);

        Logger::get()->logMsg("Ponto Mais finalizando execucao");
        
        gc_collect_cycles();    
    }
    
}