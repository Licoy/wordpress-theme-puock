<?php

namespace Yurun\Util\YurunHttp;

/**
 * 处理器初始化参数常量定义.
 */
abstract class HandlerOptions
{
    /**
     * Cookie 管理器数据保存到的文件名.
     */
    const COOKIE_JAR = 'cookie_jar';

    /**
     * 日志对象.
     */
    const LOGGER = 'logger';

    /**
     * 请求日志格式.
     */
    const REQUEST_LOG_FORMAT = 'request_log_format';
}
