<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QiNiuUploadController extends Controller
{
    # 七牛云图片存储

    private $allowedImgExt = ['jpg', 'png', 'jpeg', 'bmp'];

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['uploadPublicImg', 'uploadPrivateImg']]);
    }

    /**
     * 从数据库中解析相应json字符串 如果为私有存储并生成临时访问路径
     * @param string $jsonString
     * @return mixed|void
     */
    static public function  decodePath($jsonString = '')
    {
        if(!empty($jsonString)) {
            $result = json_decode($jsonString);

            if('private' == $result['access']) {
                    $disk = \Storage::disk('qiniu_private');
                if(!empty($result['savePath'])) {
                    $result['access_url'] = $disk->$disk->downloadUrl($result['savePath']);
                }
            }
            return $result;
        }
        return ;
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
            return false;
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
            $result['access_url'] = $disk->privateDownloadUrl($savePath);
        }
        return $result;

    }


    # 图片上传

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

        return $this->encodePath($file, $savePath, $public);
    }


    /**
     * 上传图片 至 公有bucket
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPublicImg(Request $request)
    {

        $file = $request->file('source');
        $ext = $file->getClientOriginalExtension();
        # 扩展名校验
        if(! in_array(strtolower($ext), $this->allowedImgExt)) {
            return $this->warning('非法扩展名,只接收jpg,png,jpeg,bmp图片格式!');
        }
        if(!empty($file) && $file->isValid()) {

            $result = $this->uploadImg($file, true);
            if(!$result) {
               return $this->warning('文件上传失败!');
            }
            return $this->ok($result);

        }
        return $this->warning('请检查上传文件是否存在!');
    }

    /**
     * 上传图片 至 公有bucket
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPrivateImg(Request $request)
    {
        $file = $request->file('source');
        $ext = $file->getClientOriginalExtension();
        # 扩展名校验
        if(! in_array(strtolower($ext), $this->allowedImgExt)) {
            return $this->warning('非法扩展名,只接收jpg,png,jpeg,bmp图片格式!');
        }
        if(!empty($file) && $file->isValid()) {

            $result = $this->uploadImg($file, false);
            if(!$result) {
                return $this->warning('文件上传失败!');
            }
            return $this->ok($result);
        }
        return $this->warning('请检查上传文件');
    }
}
