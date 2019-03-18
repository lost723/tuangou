<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/14
 * Time: 18:38
 */

namespace App\Http\Controllers\Log;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;

class PayLog
{

    public function test()
    {
       echo "test log";
    }

    /**
     * 创建新的 Monolog 通道
     * @param $name
     * @param $arguments
     * # @param
     */
    public static function __callStatic($name, $arguments)
    {
        $logger = new MonologLogger('payLog');
//        $logger = app('log')->getMonolog();
        $storage_path = storage_path()."/logs/Paylog/paylog";
        $payLogHandler = new RotatingFileHandler($storage_path, 0, MonologLogger::DEBUG);
        $payLogHandler->setFormatter(new JsonFormatter());//JsonFormatter
        $logger->pushHandler($payLogHandler);

        $logger->{$name}($arguments[0], $arguments[1]);
    }

}


