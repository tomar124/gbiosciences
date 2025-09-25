<?php
namespace GuzzleHttp\Ring\Client;

use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Exception\ConnectException;
use GuzzleHttp\Ring\Exception\RingException;
use GuzzleHttp\Stream\LazyOpenStream;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Creates curl resources from a request
 */
class CurlFactory
{
    /**
     * Simple logger for debugging cURL requests
     */
    private static function logRequest($msg) {
        $logfile = 'D:/xampp/htdocs/gbiosciences/system/storage/logs/curl_debug.log';
        file_put_contents($logfile, date('Y-m-d H:i:s') . " " . $msg . "\n", FILE_APPEND);
    }

    /**
     * Creates a cURL handle, header resource, and body resource based on a
     * transaction.
     *
     * @param array         $request Request hash
     * @param null|resource $handle  Optionally provide a curl handle to modify
     *
     * @return array Returns an array of the curl handle, headers array, and
     *               response body handle.
     * @throws \RuntimeException when an option cannot be applied
     */
    public function __invoke(array $request, $handle = null)
    {
        $headers = [];
        $options = $this->getDefaultOptions($request, $headers);
        $this->applyMethod($request, $options);

        if (isset($request['client'])) {
            $this->applyHandlerOptions($request, $options);
        }

        $this->applyHeaders($request, $options);
        unset($options['_headers']);

        // Add handler options from the request's configuration options
        if (isset($request['client']['curl'])) {
            $options = $this->applyCustomCurlOptions(
                $request['client']['curl'],
                $options
            );
        }

        if (!$handle) {
            $handle = curl_init();
        }

        $body = $this->getOutputBody($request, $options);
        curl_setopt_array($handle, $options);

        return [$handle, &$headers, $body];
    }

    /**
     * Creates a response hash from a cURL result.
     */
    public static function createResponse(
        callable $handler,
        array $request,
        array $response,
        array $headers,
        $body
    ) {
        if (isset($response['transfer_stats']['url'])) {
            $response['effective_url'] = $response['transfer_stats']['url'];
        }

        if (!empty($headers)) {
            $startLine = explode(' ', array_shift($headers), 3);
            $headerList = Core::headersFromLines($headers);
            $response['headers'] = $headerList;
            $response['version'] = isset($startLine[0]) ? substr($startLine[0], 5) : null;
            $response['status'] = isset($startLine[1]) ? (int) $startLine[1] : null;
            $response['reason'] = isset($startLine[2]) ? $startLine[2] : null;
            $response['body'] = $body;
            Core::rewindBody($response);
        }

        return !empty($response['curl']['errno']) || !isset($response['status'])
            ? self::createErrorResponse($handler, $request, $response)
            : $response;
    }

    private static function createErrorResponse(
        callable $handler,
        array $request,
        array $response
    ) {
        static $connectionErrors = [
            CURLE_OPERATION_TIMEOUTED  => true,
            CURLE_COULDNT_RESOLVE_HOST => true,
            CURLE_COULDNT_CONNECT      => true,
            CURLE_SSL_CONNECT_ERROR    => true,
            CURLE_GOT_NOTHING          => true,
        ];

        // Retry when nothing is present or when curl failed to rewind.
        if (!isset($response['err_message'])
            && (empty($response['curl']['errno'])
                || $response['curl']['errno'] == 65)
        ) {
            return self::retryFailedRewind($handler, $request, $response);
        }

        $message = isset($response['err_message'])
            ? $response['err_message']
            : sprintf('cURL error %s: %s',
                $response['curl']['errno'],
                isset($response['curl']['error'])
                    ? $response['curl']['error']
                    : 'See http://curl.haxx.se/libcurl/c/libcurl-errors.html');

        // Log curl error
        $uri = isset($request['uri']) ? (string)$request['uri'] : 'unknown';
        self::logRequest("Curl error for URL: {$uri} -> {$message}");

        $error = isset($response['curl']['errno'])
            && isset($connectionErrors[$response['curl']['errno']])
            ? new ConnectException($message)
            : new RingException($message);

        return $response + [
            'status'  => null,
            'reason'  => null,
            'body'    => null,
            'headers' => [],
            'error'   => $error,
        ];
    }

    private function getOutputBody(array $request, array &$options)
    {
        if (isset($options[CURLOPT_WRITEFUNCTION])) {
            return $request['client']['save_to'];
        }

        if (isset($options[CURLOPT_FILE])) {
            return $options[CURLOPT_FILE];
        }

        if ($request['http_method'] != 'HEAD') {
            return $options[CURLOPT_FILE] = fopen('php://temp', 'w+');
        }

        return null;
    }

    private function getDefaultOptions(array $request, array &$headers)
    {
        $url = Core::url($request);

        // Log the outgoing request URL
        self::logRequest("Requesting URL: " . $url);

        $startingResponse = false;

        $options = [
            '_headers'             => $request['headers'],
            CURLOPT_CUSTOMREQUEST  => $request['http_method'],
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HEADER         => false,
            CURLOPT_CONNECTTIMEOUT => 150,
            CURLOPT_HEADERFUNCTION => function ($ch, $h) use (&$headers, &$startingResponse) {
                $value = trim($h);
                if ($value === '') {
                    $startingResponse = true;
                } elseif ($startingResponse) {
                    $startingResponse = false;
                    $headers = [$value];
                } else {
                    $headers[] = $value;
                }
                return strlen($h);
            },
        ];

        if (isset($request['version'])) {
            if ($request['version'] == 2.0) {
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2_0;
            } else if ($request['version'] == 1.1) {
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
            } else {
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
            }
        }

        if (defined('CURLOPT_PROTOCOLS')) {
            $options[CURLOPT_PROTOCOLS] = CURLPROTO_HTTP | CURLPROTO_HTTPS;
        }

        return $options;
    }

    private function applyMethod(array $request, array &$options)
    {
        if (isset($request['body'])) {
            $this->applyBody($request, $options);
            return;
        }

        switch ($request['http_method']) {
            case 'PUT':
            case 'POST':
                if (!Core::hasHeader($request, 'Content-Length')) {
                    $options[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
                }
                break;
            case 'HEAD':
                $options[CURLOPT_NOBODY] = true;
                unset(
                    $options[CURLOPT_WRITEFUNCTION],
                    $options[CURLOPT_READFUNCTION],
                    $options[CURLOPT_FILE],
                    $options[CURLOPT_INFILE]
                );
        }
    }

    private function applyBody(array $request, array &$options)
    {
        $contentLength = Core::firstHeader($request, 'Content-Length');
        $size = $contentLength !== null ? (int) $contentLength : null;

        if (($size !== null && $size < 1000000) ||
            isset($request['client']['curl']['body_as_string']) ||
            is_string($request['body'])
        ) {
            $options[CURLOPT_POSTFIELDS] = Core::body($request);
            $this->removeHeader('Content-Length', $options);
            $this->removeHeader('Transfer-Encoding', $options);
        } else {
            $options[CURLOPT_UPLOAD] = true;
            if ($size !== null) {
                $options[CURLOPT_INFILESIZE] = $size;
                $this->removeHeader('Content-Length', $options);
            }
            $this->addStreamingBody($request, $options);
        }

        if (!Core::hasHeader($request, 'Expect')) {
            $options[CURLOPT_HTTPHEADER][] = 'Expect:';
        }

        if (!Core::hasHeader($request, 'Content-Type')) {
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type:';
        }
    }

    private function addStreamingBody(array $request, array &$options)
    {
        $body = $request['body'];

        if ($body instanceof StreamInterface) {
            $options[CURLOPT_READFUNCTION] = function ($ch, $fd, $length) use ($body) {
                return (string) $body->read($length);
            };
            if (!isset($options[CURLOPT_INFILESIZE])) {
                if ($size = $body->getSize()) {
                    $options[CURLOPT_INFILESIZE] = $size;
                }
            }
        } elseif (is_resource($body)) {
            $options[CURLOPT_INFILE] = $body;
        } elseif ($body instanceof \Iterator) {
            $buf = '';
            $options[CURLOPT_READFUNCTION] = function ($ch, $fd, $length) use ($body, &$buf) {
                if ($body->valid()) {
                    $buf .= $body->current();
                    $body->next();
                }
                $result = (string) substr($buf, 0, $length);
                $buf = substr($buf, $length);
                return $result;
            };
        } else {
            throw new \InvalidArgumentException('Invalid request body provided');
        }
    }

    private function applyHeaders(array $request, array &$options)
    {
        foreach ($options['_headers'] as $name => $values) {
            foreach ($values as $value) {
                $options[CURLOPT_HTTPHEADER][] = "$name: $value";
            }
        }

        if (!Core::hasHeader($request, 'Accept')) {
            $options[CURLOPT_HTTPHEADER][] = 'Accept:';
        }
    }

    private function applyCustomCurlOptions(array $config, array $options)
    {
        $curlOptions = [];
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                $curlOptions[$key] = $value;
            }
        }

        return $curlOptions + $options;
    }

    private function removeHeader($name, array &$options)
    {
        foreach (array_keys($options['_headers']) as $key) {
            if (!strcasecmp($key, $name)) {
                unset($options['_headers'][$key]);
                return;
            }
        }
    }

    private function applyHandlerOptions(array $request, array &$options)
    {
        foreach ($request['client'] as $key => $value) {
            switch ($key) {
            case 'verify':
                if ($value === false) {
                    unset($options[CURLOPT_CAINFO]);
                    $options[CURLOPT_SSL_VERIFYHOST] = 0;
                    $options[CURLOPT_SSL_VERIFYPEER] = false;
                    continue;
                }
                $options[CURLOPT_SSL_VERIFYHOST] = 2;
                $options[CURLOPT_SSL_VERIFYPEER] = true;
                if (is_string($value)) {
                    $options[CURLOPT_CAINFO] = $value;
                    if (!file_exists($value)) {
                        throw new \InvalidArgumentException(
                            "SSL CA bundle not found: $value"
                        );
                    }
                }
                break;

            case 'decode_content':
                if ($value === false) {
                    continue;
                }
                $accept = Core::firstHeader($request, 'Accept-Encoding');
                if ($accept) {
                    $options[CURLOPT_ENCODING] = $accept;
                } else {
                    $options[CURLOPT_ENCODING] = '';
                    $options[CURLOPT_HTTPHEADER][] = 'Accept-Encoding:';
                }
                break;

            case 'save_to':
                if (is_string($value)) {
                    if (!is_dir(dirname($value))) {
                        throw new \RuntimeException(sprintf(
                            'Directory %s does not exist for save_to value of %s',
                            dirname($value),
                            $value
                        ));
                    }
                    $value = new LazyOpenStream($value, 'w+');
                }
                if ($value instanceof StreamInterface) {
                    $options[CURLOPT_WRITEFUNCTION] =
                        function ($ch, $write) use ($value) {
                            return $value->write($write);
                        };
                } elseif (is_resource($value)) {
                    $options[CURLOPT_FILE] = $value;
                } else {
                    throw new \InvalidArgumentException('save_to must be a '
                        . 'GuzzleHttp\Stream\StreamInterface or resource');
                }
                break;

            case 'timeout':
                if (defined('CURLOPT_TIMEOUT_MS')) {
                    $options[CURLOPT_TIMEOUT_MS] = $value * 1000;
                } else {
                    $options[CURLOPT_TIMEOUT] = $value;
                }
                break;

            case 'connect_timeout':
                if (defined('CURLOPT_CONNECTTIMEOUT_MS')) {
                    $options[CURLOPT_CONNECTTIMEOUT_MS] = $value * 1000;
                } else {
                    $options[CURLOPT_CONNECTTIMEOUT] = $value;
                }
                break;

            case 'proxy':
                if (!is_array($value)) {
                    $options[CURLOPT_PROXY] = $value;
                } elseif (isset($request['scheme'])) {
                    $scheme = $request['scheme'];
                    if (isset($value[$scheme])) {
                        $options[CURLOPT_PROXY] = $value[$scheme];
                    }
                }
                break;

            case 'cert':
                if (is_array($value)) {
                    $options[CURLOPT_SSLCERTPASSWD] = $value[1];
                    $value = $value[0];
                }
                if (!file_exists($value)) {
                    throw new \InvalidArgumentException(
                        "SSL certificate not found: {$value}"
                    );
                }
                $options[CURLOPT_SSLCERT] = $value;
                break;

            case 'ssl_key':
                if (is_array($value)) {
                    $options[CURLOPT_SSLKEYPASSWD] = $value[1];
                    $value = $value[0];
                }
                if (!file_exists($value)) {
                    throw new \InvalidArgumentException(
                        "SSL private key not found: {$value}"
                    );
                }
                $options[CURLOPT_SSLKEY] = $value;
                break;

            case 'progress':
                if (!is_callable($value)) {
                    throw new \InvalidArgumentException(
                        'progress client option must be callable'
                    );
                }
                $options[CURLOPT_NOPROGRESS] = false;
                $options[CURLOPT_PROGRESSFUNCTION] =
                    function () use ($value) {
                        $args = func_get_args();
                        if (is_resource($args[0])) {
                            array_shift($args);
                        }
                        call_user_func_array($value, $args);
                    };
                break;

            case 'debug':
                if ($value) {
                    $options[CURLOPT_STDERR] = Core::getDebugResource($value);
                    $options[CURLOPT_VERBOSE] = true;
                }
                break;
            }
        }
    }

    private static function retryFailedRewind(
        callable $handler,
        array $request,
        array $response
    ) {
        if (!isset($request['body'])) {
            $response['err_message'] = 'No response was received for a request '
                . 'with no body.';
            return self::createErrorResponse($handler, $request, $response);
        }

        if (!Core::rewindBody($request)) {
            $response['err_message'] = 'The connection unexpectedly failed '
                . 'and the request body could not be rewound.';
            return self::createErrorResponse($handler, $request, $response);
        }

        if (!isset($request['curl']['retries'])) {
            $request['curl']['retries'] = 1;
        } elseif ($request['curl']['retries'] == 2) {
            $response['err_message'] = 'The cURL request was retried 3 times and failed.';
            return self::createErrorResponse($handler, $request, $response);
        } else {
            $request['curl']['retries']++;
        }

        return $handler($request);
    }
}