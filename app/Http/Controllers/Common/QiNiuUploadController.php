<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QiNiuUploadController extends Controller
{
    # 七牛云图片存储
    private $allowedImgExt = ['jpg', 'png', 'jpeg', 'bmp'];

    /**
     * 从数据库中解析相应json字符串 如果为私有存储并生成临时访问路径
     * @param string $jsonString
     * @return mixed|void
     */
     public function  accessPath($jsonString = '')
    {
        if(!empty($jsonString)) {
            $result = json_decode($jsonString, true);
            if('private' == $result['access']) {
                $disk = Storage::disk('qiniu_private');
            }
            else {
                $disk = Storage::disk('qiniu_public');
            }
            return $disk->imagePreviewUrl($result['path']);
        }
        return "";
    }

    static function savePath($path = [])
    {
        if(!is_array($path)) {
            throw new \Exception('解析格式不正确');
        }
        $savePath = [
            'access'    =>  $path['access'],
            'path'      =>  $path['savePath'],
        ];
        return json_encode($savePath, true);
    }

    //  定义PictureStyle
    static function getUrl($jsonString = '', $option)
    {
        if(is_array($option)) {
            $option = implode('|', $option);
        }
        return self::decodePath($jsonString).$option;
    }


    /**
     * 上传文件路径整理 写入数据库时直接 通过json_encode 编码写入
     * @param $file 文件句柄
     * @param $savePath 文件保存路径
     * @param bool $public 是否上传至公有bucket
     * @return array|bool
     */
    public function encodePath($file, $savePath, $public = true)
    {
        if(empty($savePath)) {
            throw new \Exception('保存路径不能为空！');
        }

        $result = [];
        $data = explode('/', $savePath);
        $filename = end($data);
        if($public) {
            $contents = file_get_contents($file);

            $disk = \Storage::disk('qiniu_public');
            $disk->put($savePath,$contents);

            $result['access']   = 'public';
            $result['filename'] = $filename;
            $result['savePath'] = $savePath;
            $result['access_url'] = $disk->downloadUrl($savePath);
        }
        else {
            $contents = file_get_contents($file);
            $disk = \Storage::disk('qiniu_private');
            $disk->put($savePath,$contents);

            $result['access'] = 'private';
            $result['filename'] = $filename;
            $result['savePath'] = $savePath;
            $result['access_url'] = $disk->downloadUrl($savePath);
         }
        return $result;

    }

    /**
     * 图片上传公共接口
     * @param $file 文件句柄
     * @param bool $public 是否上传到公有bucket
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function uploadImg($file, $public = true)
    {
        $ext = $file->getClientOriginalExtension();
        $filename = $file->getClientOriginalName();
        # 重新生成文件名
        $newFileName = md5($filename . time() . mt_rand(1, 10000)) . '.' . $ext;
        $savePath = 'images/' .date('Ymd').'/'.$newFileName;
        $result = $this->encodePath($file, $savePath, $public);
        return $result;
    }


    /**
     * 上传图片 至 公有bucket
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPublicImg(Request $request)
    {

        try{
            $file = $request->file('source');
            $ext = $file->getClientOriginalExtension();
            # 扩展名校验
            if(! in_array(strtolower($ext), $this->allowedImgExt)) {
                throw new \Exception('非法扩展名,只接收jpg,png,jpeg,bmp图片格式!');
            }
            $result = $this->uploadImg($file, true);
            return $this->ok($result);
        }
        catch (\Exception $exception) {
            $this->warning($exception->getMessage());
        }
    }

    /**
     * 上传图片 至 私有bucket
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPrivateImg(Request $request)
    {
        try{
            $file = $request->file('source');
            $ext = $file->getClientOriginalExtension();
            # 扩展名校验
            if(! in_array(strtolower($ext), $this->allowedImgExt)) {
                throw new \Exception('非法扩展名,只接收jpg,png,jpeg,bmp图片格式!');
            }
            $result = $this->uploadImg($file, false);
            return $this->ok($result);
        }
        catch (\Exception $exception) {
            $this->warning($exception->getMessage());
        }
    }
}
