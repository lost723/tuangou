<?php
namespace App\Utils;

use App\Http\Controllers\Common\QiNiuUploadController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait ImageView{
    /**
     * @param $location 存储位置 七牛云存储的路径
     * @param $op       选项参数 需在七牛云后台配置
     * @param int $access 访问限制 默认公有 私有access=1
     * # @param
     */
    public function ImageViewWithOption($location, $op)
    {
        return QiNiuUploadController::accessPathWithOption($location, $op);
    }

    /**
     * @param $location 存储位置
     * @param $width    宽度 单位px
     * @param $height   高度 单位px
     * @param int $access 访问属性  默认公有0 私有access=1
     * # @param
     */
    public function ImageViewWithInc($location, $width, $height, $access=0)
    {
        return QiNiuUploadController::accessPathWithInc($location, $width, $height, $access);
    }


    /**
     * 生成核销二维码
     * @param $info     内容
     * @param int $size 二维码大小
     * @return string
     */
    public function createQrCode($info, $size=200)
    {//->merge(public_path('images/logo.png'), .3, true)
        return "data:image/png;base64,".base64_encode(QrCode::format('png')->errorCorrection('L')->size(500)->generate($info));
    }

}