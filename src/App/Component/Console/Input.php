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
     * @return void
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
     * open resource
     *
     * @return Input
     */
    protected function openStream(): Input
    {
        if (!is_resource($this->streamHandler)) {
            $this->streamHandler = fopen(
                $this->getStreamName(),
                $this->streamMode,
                false
            );
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
        if (is_resource($this->streamHandler)) {
            fclose($this->streamHandler);
        }
        return $this;
    }
}
