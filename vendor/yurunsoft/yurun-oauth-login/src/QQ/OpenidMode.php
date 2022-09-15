<?php

namespace Yurun\OAuthLogin\QQ;

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
     * 优先使用unionid，否则使用openid.
     */
    const UNION_ID_FIRST = 3;
}
