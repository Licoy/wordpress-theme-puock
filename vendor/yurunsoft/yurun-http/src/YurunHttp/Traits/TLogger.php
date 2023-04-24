<?php

namespace Yurun\Util\YurunHttp\Traits;

use Psr\Log\LoggerInterface;
use Yurun\Util\YurunHttp\HandlerOptions;

trait TLogger
{
    /**
     * @return LoggerInterface|null
     */
    protected function getLogger()
    {
        return isset($this->options[HandlerOptions::LOGGER]) ? $this->options[HandlerOptions::LOGGER] : null;
    }

    /**
     * @param \Yurun\Util\YurunHttp\Http\Request  $request
     * @param \Yurun\Util\YurunHttp\Http\Response $response
     *
     * @return void
     */
    protected function logRequest($request, $response)
    {
        $logger = $this->getLogger();
        if (!$logger)
        {
            return;
        }
        if (isset($this->options[HandlerOptions::REQUEST_LOG_FORMAT]))
        {
            $requestLogFormat = $this->options[HandlerOptions::REQUEST_LOG_FORMAT];
        }
        else
        {
            $requestLogFormat = <<<'STR'
Request: [{method}] {url}
Response: statusCode: [{status_code}], contentLength: {content_length}, errno: {errno}, error: {error}, useTime: {time}
STR;
        }

        $message = preg_replace_callback('/\{([^\}]+)\}/', function ($matches) use ($request, $response) {
            switch ($matches[1])
            {
                case 'method':
                    return $request->getMethod();
                case 'url':
                    return $request->getUri()->__toString();
                case 'request_body':
                    return $request->getBody()->__toString();
                case 'request_headers':
                    $headers = [];
                    foreach ($request->getHeaders() as $name => $value)
                    {
                        $headers[] = $name . ': ' . implode(',', $value);
                    }

                    return implode("\r\n", $headers);
                case 'version':
                    return $request->getProtocolVersion();
                case 'response_body':
                    return $response->getBody()->__toString();
                case 'errno':
                    return (string) $response->getErrno();
                case 'error':
                    return $response->getError();
                case 'response_headers':
                    $headers = [];
                    foreach ($response->getHeaders() as $name => $value)
                    {
                        $headers[] = $name . ': ' . implode(',', $value);
                    }

                    return implode("\r\n", $headers);
                case 'status_code':
                    return (string) $response->getStatusCode();
                case 'reason_phrase':
                    return $response->getReasonPhrase();
                case 'time':
                    return round($response->getTotalTime(), 3) . 's';
                case 'content_length':
                    return (string) $response->getBody()->getSize();
            }

            return '';
        }, $requestLogFormat);
        $logger->info($message);
    }
}
