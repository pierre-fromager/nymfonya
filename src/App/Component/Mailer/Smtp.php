<?php

namespace App\Component\Mailer;

use Exception;
use Nymfonya\Component\Container;
use Nymfonya\Component\Config;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class Smtp
{

    const _MAILER = 'mailer';
    const _SMTP = 'smtp';
    const _HOST = 'host';
    const _PORT = 'port';
    const _USERNAME = 'username';
    const _PASSWORD = 'password';
    const _ENCRYPTION = 'encryption';

    /**
     * transport mailer
     *
     * @var Swift_SmtpTransport
     */
    private $transport;

    /**
     * mailer instance
     *
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * mail message
     *
     * @var Swift_Message
     */
    private $message;

    /**
     * mail message from
     *
     * @var array
     */
    private $from;

    /**
     * mail message to
     *
     * @var array
     */
    private $to;

    /**
     * mailer send error
     *
     * @var Boolean
     */
    private $error;


    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $config = $container->getService(Config::class);
        $mailerConfig = $config->getSettings(self::_MAILER);
        $this->init($mailerConfig[self::_SMTP]);
    }

    /**
     * set message initiators
     *
     * @param array $from
     * @return Smtp
     */
    public function setFrom(array $from): Smtp
    {
        $this->from = $from;
        return $this;
    }

    /**
     * set message recipients
     *
     * @param array $to
     * @return Smtp
     */
    public function setTo(array $to): Smtp
    {
        $this->to = $to;
        return $this;
    }

    /**
     * prepare message
     *
     * @param string $title
     * @param string $body
     * @return Smtp
     */
    public function setMessage(string $title, string $body): Smtp
    {
        $this->message = new Swift_Message($title);
        $this->message
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setBody($body);
        return $this;
    }

    /**
     * true means message was not sent
     *
     * @return boolean
     */
    public function isError(): bool
    {
        return $this->error === true;
    }

    /**
     * send message
     *
     * @return Smtp
     */
    public function sendMessage(): Smtp
    {
        $this->mailer->send($this->message);
        return $this;
    }

    protected function init(array $transportConfig): Smtp
    {
        $this->setTransport($transportConfig)->setMailer();
        return $this;
    }

    /**
     * prepare mailer transport
     *
     * @param array $transportConfig
     * @return Smtp
     */
    protected function setTransport(array $transportConfig): Smtp
    {
        $this->checkConfig($transportConfig);
        $host = $transportConfig[self::_HOST];
        $port = $transportConfig[self::_PORT];
        $username = $transportConfig[self::_USERNAME];
        $password = $transportConfig[self::_PASSWORD];
        $encryption = $transportConfig[self::_ENCRYPTION];
        $this->transport = new Swift_SmtpTransport($host, $port, $encryption);
        $this->transport->setUsername($username)->setPassword($password);
        return $this;
    }

    /**
     * check if transport config is ready
     *
     * @param array $transportConfig
     * @return Smtp
     */
    protected function checkConfig(array $transportConfig): Smtp
    {
        $isValid = isset($transportConfig[self::_HOST])
            && isset($transportConfig[self::_PORT])
            && isset($transportConfig[self::_USERNAME])
            && isset($transportConfig[self::_PASSWORD])
            && isset($transportConfig[self::_ENCRYPTION]);
        if (false === $isValid) {
            throw new Exception('Invalid transport config');
        }
        return $this;
    }

    /**
     * prepare mailer
     *
     * @return Smtp
     */
    protected function setMailer(): Smtp
    {
        $mailSendResult = $this->mailer = new Swift_Mailer($this->transport);
        $this->error = ($mailSendResult === 0);
        return $this;
    }
}
