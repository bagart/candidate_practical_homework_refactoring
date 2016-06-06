<?php
namespace Language\Module\Traits;
use Monolog;

trait Logger
{
    /**
     * @var null|\Psr\Log\LoggerInterface
     */
    private $logger = null;

    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    private function getLoggerDefault()
    {
        $logger = new Monolog\Logger('Language');
        $logger
            ->pushHandler(
                new Monolog\Handler\StreamHandler(
                    'php://stderr',
                    Monolog\Logger::WARNING
                )
            )
            ->pushHandler(
                new Monolog\Handler\StreamHandler(
                    'php://stdout',
                    Monolog\Logger::INFO,
                    false
                )
            );
        
        return $logger;
    }

    public function getLogger()
    {
        if (!$this->logger) {
            $this->setLogger(
                $this->getLoggerDefault()
            );
        }
        
        return $this->logger;
    }
}