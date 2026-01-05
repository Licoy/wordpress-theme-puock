<?php

namespace Puock\Theme\oauth;

use InvalidArgumentException;

class RainbowOAuth
{
    public $appid;

    public $appSecret;

    public $callbackUrl;

    public $state;

    public $result = [];

    public $accessToken;

    public $openid;


    public function __construct($appid = null, $appSecret = null, $callbackUrl = null)
    {
        $this->appid = trim((string)($appid ?? ''));
        $this->appSecret = trim((string)($appSecret ?? ''));
        $this->callbackUrl = trim((string)($callbackUrl ?? ''));
    }

    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $this->state = $state ?: md5(uniqid('', true));

        $redirectUri = $callbackUrl ?: $this->callbackUrl;
        if (empty($redirectUri))
        {
            throw new InvalidArgumentException('callbackUrl 不能为空');
        }

        // Append state for CSRF validation
        $redirectUriWithState = add_query_arg('state', $this->state, $redirectUri);

        $socialType = $this->getSocialType();

        $data = $this->request([
            'act' => 'login',
            'appid' => $this->appid,
            'appkey' => $this->appSecret,
            'type' => $socialType,
            'redirect_uri' => $redirectUriWithState,
        ]);

        $url = (string)($data['url'] ?? '');
        if (empty($url))
        {
            throw new InvalidArgumentException('彩虹聚合登录返回的跳转地址为空');
        }

        return $url;
    }

    public function getAccessToken($storeState = '', $code = null, $state = null)
    {
        $state = $state ?? ($_GET['state'] ?? '');
        if ((string)$storeState !== (string)$state)
        {
            throw new InvalidArgumentException('state验证失败');
        }

        $code = $code ?? ($_GET['code'] ?? '');
        if (empty($code))
        {
            throw new InvalidArgumentException('缺少回调参数 code');
        }

        $socialType = $this->getSocialType();

        $data = $this->request([
            'act' => 'callback',
            'appid' => $this->appid,
            'appkey' => $this->appSecret,
            'type' => $socialType,
            'code' => $code,
        ]);

        $this->result = $data;
        $this->accessToken = (string)($data['access_token'] ?? '');
        $this->openid = (string)($data['social_uid'] ?? '');

        if (empty($this->openid))
        {
            throw new InvalidArgumentException('彩虹聚合登录返回 social_uid 为空');
        }

        return $this->accessToken;
    }

    public function getUserInfo($accessToken = null)
    {
        if (!empty($this->result))
        {
            return $this->result;
        }

        if (empty($this->openid))
        {
            throw new InvalidArgumentException('openid 为空，无法查询用户信息');
        }

        $socialType = $this->getSocialType();

        $data = $this->request([
            'act' => 'query',
            'appid' => $this->appid,
            'appkey' => $this->appSecret,
            'type' => $socialType,
            'social_uid' => $this->openid,
        ]);

        $this->result = $data;
        return $this->result;
    }

    private function getSocialType(): string
    {
        $allow = ['qq', 'wx', 'alipay', 'sina', 'baidu', 'huawei', 'xiaomi', 'douyin', 'bilibili', 'dingtalk'];

        $providerType = sanitize_key($_GET['type'] ?? '');
        if (in_array($providerType, $allow, true)) {
            return $providerType;
        }

        if (str_starts_with($providerType, 'ccy_'))
        {
            $t = substr($providerType, strlen('ccy_'));
            if (in_array($t, $allow, true))
            {
                return $t;
            }
        }

        return 'qq';
    }

    private function request(array $query): array
    {
        if (empty($this->appid) || empty($this->appSecret))
        {
            throw new InvalidArgumentException('彩虹聚合登录 AppID/AppKey 未配置');
        }

        $apiBase = (string)(\pk_get_option('oauth_ccy_api') ?: 'https://u.cccyun.cc');
        $apiBase = rtrim($apiBase, '/');
        if (!str_starts_with($apiBase, 'http://') && !str_starts_with($apiBase, 'https://')) {
            throw new InvalidArgumentException('接口地址格式不正确');
        }

        $apiUrl = $apiBase . '/connect.php';
        $url = add_query_arg($query, $apiUrl);

        $resp = wp_remote_get($url, [
            'timeout' => 15,
            'redirection' => 0,
        ]);

        if (is_wp_error($resp))
        {
            throw new InvalidArgumentException('请求彩虹聚合登录失败：' . $resp->get_error_message());
        }

        $body = wp_remote_retrieve_body($resp);
        $data = json_decode($body, true);
        if (!is_array($data))
        {
            throw new InvalidArgumentException('彩虹聚合登录返回数据解析失败');
        }

        $code = (int)($data['code'] ?? -1);
        if ($code !== 0)
        {
            $msg = (string)($data['msg'] ?? '请求失败');
            throw new InvalidArgumentException($msg);
        }

        return $data;
    }
}
