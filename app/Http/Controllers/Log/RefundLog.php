<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/15
 * Time: 16:45
 */

namespace App\Http\Controllers\Log;

//use App\Http\Controllers\Controller;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;

class RefundLog
{
    /**
     * 创建新的 Monolog 通道
     * @param $name
     * @param $arguments
     * # @param
     */
    public static function __callStatic($name, $arguments)
    {
        $logger = new MonologLogger('refundlog');
//        $logger = app('log')->getMonolog();
        $storage_path = storage_path()."/logs/Paylog/refundlog";
        $payLogHandler = new RotatingFileHandler($storage_path, 0, MonologLogger::DEBUG);
        $payLogHandler->setFormatter(new JsonFormatter());//JsonFormatter
        $logger->pushHandler($payLogHandler);

        $logger->{$name}($arguments[0], $arguments[1]);
    }

}
