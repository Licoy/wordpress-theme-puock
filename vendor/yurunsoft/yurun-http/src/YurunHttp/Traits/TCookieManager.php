<?php

namespace Yurun\Util\YurunHttp\Traits;

use Yurun\Util\YurunHttp\Cookie\CookieJar;
use Yurun\Util\YurunHttp\Cookie\CookieManager;
use Yurun\Util\YurunHttp\HandlerOptions;

trait TCookieManager
{
    /**
     * Cookie 管理器.
     *
     * @var CookieManager
     */
    protected $cookieManager;

    /**
     * Cookie 管理器.
     *
     * @var CookieJar
     */
    protected $cookieJar;

    /**
     * @return void
     */
    private function initCookieManager()
    {
        $this->cookieJar = $cookieJar = new CookieJar($this->cookieManager = new CookieManager());
        if (isset($this->options[HandlerOptions::COOKIE_JAR]) && \is_string($this->options[HandlerOptions::COOKIE_JAR]))
        {
            $cookieJar->load($this->options[HandlerOptions::COOKIE_JAR]);
        }
    }

    /**
     * Get cookie 管理器.
     *
     * @return CookieManager
     */
    public function getCookieManager()
    {
        return $this->cookieManager;
    }

    /**
     * Get cookie 管理器.
     *
     * @return CookieJar
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }

    /**
     * 保存 Cookie.
     *
     * @return void
     */
    protected function saveCookieJar()
    {
        if (isset($this->options[HandlerOptions::COOKIE_JAR]) && \is_string($this->options[HandlerOptions::COOKIE_JAR]))
        {
            $this->cookieJar->save($this->options[HandlerOptions::COOKIE_JAR]);
        }
    }
}
