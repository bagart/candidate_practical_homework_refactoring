<?php

class LanguageBatchBoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Language\LanguageBatchBo
     */
    private $app;

    public function getApp()
    {
        if (!$this->app) {
            $this->app = new \Language\LanguageBatchBo();
            $this->app->setLogger(
                new ExceptionLogger()
            );
        }
        
        return $this->app;
    }

    public function test_generateLanguageFiles()
    {
        try {
            $this->getApp()->generateLanguageFiles();
            $this->assertTrue(true);
        } catch (\Language\Iface\LanguageException $e) {
            $this->assertTrue(false);
        }
    }

    public function test_generateAppletLanguageXmlFiles()
    {
        try {
            $this->getApp()->generateAppletLanguageXmlFiles();
            $this->assertTrue(true);
        } catch (\Language\Iface\LanguageException $e) {
            $this->assertTrue(false);
        }
    }
    
    public function testLogger()
    {
        $this->assertTrue(
            $this->getApp()->getLogger() instanceof ExceptionLogger
        );
        
        try {
            $this->getApp()->getLogger()->info('all ok');
            $this->assertTrue(true);
        } catch (\Language\Iface\LanguageException $e) {
            $this->assertTrue(false);
        }
        try {
            $this->getApp()->getLogger()->warning('all ok');
            $this->assertTrue(false);
        } catch (\Language\Iface\LanguageException $e) {
            $this->assertTrue(true);
        }
        
    }
}

class ExceptionLogger extends \Psr\Log\AbstractLogger
{
    private $level = \Monolog\Logger::WARNING;

    public function log($level, $message, array $context = array())
    {
        if (\Monolog\Logger::toMonologLevel($level) >= $this->level) {
            throw new \Language\Exception\ErrorResult($message);
        }
    }
}
