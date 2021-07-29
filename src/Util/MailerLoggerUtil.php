<?php
namespace App\Util;

use Symfony\Bridge\Monolog\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;

/**
 * A utility to log Swift e-mails.
 */
class MailerLoggerUtil implements Swift_Events_SendListener
{
     /**
      * A Monolog logger.
      *
      * @var LoggerInterface
      */
    protected $logger;

    /**
     * MailerLoggerUtil constructor.
     *
     * @param LoggerInterface $mailerLogger The Logger Interface.
     */
    public function __construct(LoggerInterface $mailerLogger)
    {
        $this->logger = $mailerLogger;
    }

    /**
     * This function runs before a mail message is send.
     *
     * @param Swift_Events_SendEvent $event The SwiftMailer Event.
     *
     * @return void
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $event) : void
    {
        $this->logMailMessage($event);
    }

    /**
     * This function runs when a mail message is send.
     *
     * @param Swift_Events_SendEvent $event The SwiftMailer Event.
     *
     * @return void
     */
    public function sendPerformed(Swift_Events_SendEvent $event) : void
    {
        $this->logMailMessage($event);
    }

    /**
     * Log the Swift Mail message to logger.
     *
     * @param Swift_Events_SendEvent $event The SwiftMailer Event.
     *
     * @return void
     */
    private function logMailMessage(Swift_Events_SendEvent $event) : void
    {
        $level   = $this->getLogLevel($event);
        $message = $event->getMessage();

        $this->logger->log(
            $level,
            $message->getSubject() . ' - ' . $message->getId(),
            [
                'result'  => $event->getResult(),
                'subject' => $message->getSubject(),
                'to'      => $message->getTo(),
            ]
        );
    }

    /**
     * Returns the LogLevel according to Swift Event Result.
     *
     * @param Swift_Events_SendEvent $event The SwiftMailer Event.
     *
     * @return string
     */
    private function getLogLevel(Swift_Events_SendEvent $event) : string
    {
        switch ($event->getResult()) {
            // Sending has yet to occur
            case Swift_Events_SendEvent::RESULT_PENDING:
                return LogLevel::WARNING;
                break;

            // Email is spooled, ready to be sent
            case Swift_Events_SendEvent::RESULT_SPOOLED:
                return LogLevel::NOTICE;
                break;


            // Sending failed
            case Swift_Events_SendEvent::RESULT_FAILED:
                return LogLevel::CRITICAL;
                break;

            // Sending worked, but there were some failures
            case Swift_Events_SendEvent::RESULT_TENTATIVE:
                return LogLevel::ERROR;
                break;

            // Sending was successful
            case Swift_Events_SendEvent::RESULT_SUCCESS:
                return LogLevel::INFO;
                break;

            default:
                break;
        }
    }
}


