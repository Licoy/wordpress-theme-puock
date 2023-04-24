<?php

namespace Yurun\Util\YurunHttp\Cookie;

class CookieJar
{
    /**
     * @var CookieManager
     */
    private $cookieManager;

    /**
     * @param CookieManager $cookieManager
     */
    public function __construct($cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    public function load($fileName)
    {
        if (is_file($fileName))
        {
            $data = json_decode(file_get_contents($fileName), true);
            $this->cookieManager->setCookieList($data);
        }
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    public function save($fileName)
    {
        $data = [];
        foreach ($this->cookieManager->getCookieList() as $cookieItem)
        {
            $data[] = (array) $cookieItem;
        }
        file_put_contents($fileName, json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE));
    }
}
