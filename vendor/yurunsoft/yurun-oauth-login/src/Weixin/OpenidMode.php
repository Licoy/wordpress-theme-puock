<?php

namespace Yurun\OAuthLogin\Weixin;

class OpenidMode
{
    /**
     * 使用openid.
     */
    const OPEN_ID = 1;

    /**
     * 使用unionid.
     */
    const UNION_ID = 2;

    /**
     * 优先使用unionid，如果没有则使用openid.
     */
    const UNION_ID_FIRST = 3;
}
