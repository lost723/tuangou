<?php
namespace App\Utils;

trait NetHelper
{
    /**
     * 封装curl post请求
     * @param $url
     * @param $post_data
     * @return bool|mixed
     */
    static function http_post($url, $post_data)
    {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

            try {
                $output = curl_exec($ch);
            }
            catch (Exception $exception) {
                return false;
            }
            finally {
                curl_close($ch);
            }
            return $output;
    }

    /**
     * 封装curl get请求
     * @param $url
     *
     * @return bool|mixed
     */
    static function http_get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        try {
            $output = curl_exec($ch);
        }
        catch (Exception $exception) {
//                    echo $exception->getMessage();
            return false;
        }
        finally {
//            echo "cURL Error: " . curl_error($ch);
            curl_close($ch);
        }
        if ($output === FALSE) {

            return false;
        }
        else {
            return $output;
        }
    }
}