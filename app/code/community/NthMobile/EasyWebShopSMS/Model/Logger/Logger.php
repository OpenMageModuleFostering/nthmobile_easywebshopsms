<?php

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;


class NthMobile_EasyWebShopSMS_Model_Logger_Logger extends AbstractLogger
{

    /**
     * Map PSR Log Level to Zend Log Level
     *
     * @var array
     */
    private $logMap = [
        LogLevel::ALERT     => Zend_Log::ALERT,
        LogLevel::CRITICAL  => Zend_Log::CRIT,
        LogLevel::DEBUG     => Zend_Log::DEBUG,
        LogLevel::EMERGENCY => Zend_Log::EMERG,
        LogLevel::ERROR     => Zend_Log::ERR,
        LogLevel::INFO      => Zend_Log::INFO,
        LogLevel::NOTICE    => Zend_Log::NOTICE,
        LogLevel::WARNING   => Zend_Log::WARN,
    ];

    /**
     * @var Zend_Log
     */
    private $logger;

    /**
     * @param Zend_Log $logger
     */
    public function __construct(Zend_Log $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param int    $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        if (!isset($this->logMap[$level])) {
            throw new \InvalidArgumentExcepton('Level is not supported. See "iPsr\Log\LogLevel"');
        }

        $zendLogLevel = $this->logMap[$level];

        //Proxy log
        $this->logger->log($message, $zendLogLevel);
    }
}
