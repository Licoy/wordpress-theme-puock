<?php

namespace Yurun\Util\YurunHttp\Traits;

use Psr\Http\Message\UriInterface;
use Yurun\Util\YurunHttp\Http\Psr7\Uri;

trait THandler
{
    /**
     * 处理重定向的 location.
     *
     * @param string       $location
     * @param UriInterface $currentUri
     *
     * @return UriInterface
     */
    public function parseRedirectLocation($location, $currentUri)
    {
        $locationUri = new Uri($location);
        if ('' === $locationUri->getHost())
        {
            if (!isset($location[0]))
            {
                throw new \InvalidArgumentException(sprintf('Invalid $location: %s', $location));
            }
            if ('/' === $location[0])
            {
                $uri = $currentUri->withQuery('')->withPath($location);
            }
            else
            {
                $uri = $currentUri;
                $path = $currentUri->getPath();
                if ('/' !== substr($path, -1, 1))
                {
                    $path .= '/';
                }
                $path .= $location;
                $uri = $uri->withPath($path);
            }
            $uri = $uri->withHost($currentUri->getHost())->withPort($currentUri->getPort());
        }
        else
        {
            $uri = $locationUri;
        }

        return $uri;
    }

    /**
     * 检查请求对象
     *
     * @param \Yurun\Util\YurunHttp\Http\Request[] $requests
     *
     * @return void
     */
    protected function checkRequests($requests)
    {
        foreach ($requests as $request)
        {
            if (!$request instanceof \Yurun\Util\YurunHttp\Http\Request)
            {
                throw new \InvalidArgumentException('Request must be instance of \Yurun\Util\YurunHttp\Http\Request');
            }
        }
    }
}
