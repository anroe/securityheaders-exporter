<?php

namespace App;

/**
 * Class SecurityHeaders
 * @package App
 */
class SecurityHeaders
{
    /**
     * @var string
     */
    public string $target;

    /**
     * @var string
     */
    private string $targetUrlEncoded;

    /**
     * @var string
     */
    private string $headerName = 'X-Grade';

    /**
     * SecurityHeaders constructor.
     * @param string $target
     * @throws \Exception
     */
    public function __construct(string $target)
    {
        if (!filter_var($target, FILTER_VALIDATE_URL)) {
            throw new \Exception($target . ' is not a valid URL');
        }

        $this->target = $target;
        $this->targetUrlEncoded = urlencode($target);
    }

    /**
     * @return string
     */
    public function getGrade(): string
    {
        // Used to catch Warning from get_headers(), ex : getaddrinfo failed
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            swoole_error_log(SWOOLE_LOG_WARNING, $err_msg);
        }, E_WARNING);

        $headers = get_headers(
            "https://securityheaders.com/?q={$this->targetUrlEncoded}&hide=on&followRedirects=on",
            true,
            stream_context_create(['http' => ['method'=>'HEAD']])
        );

        restore_error_handler();

        if (false === $headers) {
            // TODO : Render metrics with securityheaders_probe_success 0
        }

        if (empty($headers[$this->headerName])) {
            // TODO : Render metrics with securityheaders_probe_success 0
            swoole_error_log(SWOOLE_LOG_ERROR, 'Grade is empty for target ' . $this->target);
        }

        // TODO : Verify grade

        return $headers[$this->headerName];
    }
}