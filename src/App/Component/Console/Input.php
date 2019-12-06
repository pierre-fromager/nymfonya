<?php

namespace App\Component\Console;

class Input
{

    const STREAM_STDIN = 'php://stdin';
    const STREAM_MEMORY = 'php://memory';
    const STREAM_TEMP = 'php://temp';
    const STREAM_MODE_READ = 'r';
    const STREAM_MODE_APPEND = 'r+';
    const STREAM_MODE_WRITE = 'w';
    const STREAM_MODE_WRITE_APPEND = 'w+';
    const DEBUGER = 'phpdbg';

    /**
     * streamName
     *
     * @var string
     */
    protected $streamName;

    /**
     * streamMode
     *
     * @var string
     */
    protected $streamMode;

    /**
     * $streamHandler
     *
     * @var resource
     */
    protected $streamHandler;

    /**
     * $maxLength
     *
     * @var int
     */
    protected $maxLength;

    /**
     * instanciate
     *
     * @param string $streamName
     */
    public function __construct(
        string $streamName = self::STREAM_STDIN,
        string $streamMode = self::STREAM_MODE_WRITE_APPEND
    ) {
        $this->streamName = (php_sapi_name() == self::DEBUGER)
            ? self::STREAM_MEMORY
            : $streamName;
        $this->streamMode = $streamMode;
        $this->setMaxLength(1);
    }

    /**
     * return the input value
     *
     * @param string $forcedValue
     * @return string
     */
    public function value(string $forcedValue = ''): string
    {
        $handle = $this->getStreamHandler();
        //readline_callback_handler_install('', function () { });
        if (!empty($forcedValue)) {
            $this->setMaxLength(strlen($forcedValue));
            rewind($handle);
            fwrite($handle, $forcedValue);
        }
        $value = stream_get_contents(
            $handle,
            $this->getMaxLength(),
            0
        );
        $this->closeStream();
        return $value;
    }

    /**
     * set stream max length
     *
     * @return Input
     */
    public function setMaxLength(int $len): Input
    {
        $this->maxLength = $len;
        return $this;
    }

    /**
     * returns stream handler
     *
     * @return int
     */
    protected function getMaxLength(): int
    {
        return $this->maxLength;
    }

    /**
     * returns stream handler
     *
     * @return resource | null
     */
    protected function getStreamHandler()
    {
        $this->openStream();
        return $this->streamHandler;
    }

    /**
     * returns stream name
     *
     * @return string
     */
    protected function getStreamName(): string
    {
        return $this->streamName;
    }

    /**
     * return false if streamHandler is not a resource
     *
     * @return boolean
     */
    protected function streamable(): bool
    {
        return is_resource($this->streamHandler);
    }

    /**
     * open resource
     *
     * @return Input
     * @throws Exception
     */
    protected function openStream(): Input
    {
        if (false === $this->streamable()) {
            $this->streamHandler = fopen($this->getStreamName(), $this->streamMode, false);
        }
        if (false === $this->streamable()) {
            throw new \Exception('Cant open input stream handle');
        }
        return $this;
    }

    /**
     * close resource
     *
     * @return Input
     */
    protected function closeStream(): Input
    {
        if ($this->streamable()) {
            fclose($this->streamHandler);
        }
        return $this;
    }
}
