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

            $driver = ChromeDriver::start();
            $driver->get('https://app.pontomais.com.br/');
            sleep(5);
            
            $loginInput = $driver->findElement(WebDriverBy::XPath("//*[@title='Login*']"));
            $loginInput->sendKeys($_ENV["PM_USER"]);
            
            $passwordInput = $driver->findElement(WebDriverBy::XPath("//*[@title='Sua senha*']"));
            $passwordInput->sendKeys($_ENV["PM_PASS"]);
            
            Logger::get()->logMsg("Ponto Mais - Realizando o Login");
            $buttonLogin = $driver->findElement(WebDriverBy::XPath("//*[@type='submit']"));
            $buttonLogin->click();
            sleep(20);

            Logger::get()->logMsg("Ponto Mais - Navegando para bater o ponto");
            $driver->navigate()->to("https://app2.pontomais.com.br/registrar-ponto");
            sleep(10);

            try {
                Logger::get()->logMsg("Ponto Mais - Selecionando o endereco");
                $buttonEndereco = $driver->findElement(WebDriverBy::XPath("//a[contains(text(),'Utilizar localização do meu último registro')]"));
                $buttonEndereco->click();
                sleep(10);
            } catch (ElementNotInteractableException $e) {
                Logger::get()->logMsg("Ponto Mais - Endereco ja selecionado");
            }

            Logger::get()->logMsg("Ponto Mais - Batendo o Ponto");
            $buttonPonto = $driver->findElement(WebDriverBy::XPath("//button[contains(@class,'pm-button') and contains(@class,'pm-primary')]/span[contains(text(),'Bater ponto')]"));
            $buttonPonto->click();
            sleep(25);

            /*exit;

            
            $buttomLogin = $driver->findElement(WebDriverBy::XPath("//button[contains(text(),'Bater ponto')]"));
            $buttomLogin->click();
            sleep(5);

            $buttomLogin = $driver->findElement(WebDriverBy::XPath("//button[contains(@class, 'close')]"));
            $buttomLogin->click();
            sleep(10);

            $menuUser = $driver->findElement(WebDriverBy::XPath("//a[@title='Usuário']"));
            $menuUser->click();
            sleep(1);

            Logger::get()->logMsg("Ponto Mais - Saindo da aplicacao");
            $buttomLogout = $driver->findElement(WebDriverBy::XPath("//a[contains(text(),'Sair')]"));
            $buttomLogout->click();
            sleep(1);*/
            
            $driver->quit();

        } while($isSuccess === false AND $counterTry < 3);

        Logger::get()->logMsg("Ponto Mais finalizando execucao");
        
        gc_collect_cycles();    
    }
    
}