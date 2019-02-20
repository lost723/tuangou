<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Common\WXBaseController;

class SendTemplateMsgController extends WXBaseController
{
    const TEMPLATE_SEND_URL = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?';
    /**
     * @param $touser           接收用户openid
     * @param $template_id      模板消息id
     * @param $form_id          表单提交场景下，为 submit 事件带上的 formId；支付场景下，为本次支付的 prepay_id
     * @param $page             点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转
     * @param $data             模板内容，不填则下发空模板。具体格式请参考示例
     * @param $emphasis_keyword 模板需要放大的关键词，不填则默认无放大
     */
    public function sendTemplateMessage($touser, $template_id, $form_id, $page, $data, $emphasis_keyword)
    {
        # 请求接口access_token
        $access_token   = $this->getAccessToken(false);
        $data = [
            'access_token'      =>  $access_token,
            'touser'            =>  $touser,
            'template_id'       =>  $template_id,
            'form_id'           =>  $form_id,
            'page'              =>  $page,
            'data'              =>  $data,
            'emphasis_keyword'  =>  $emphasis_keyword,
        ];

        $result = $this->http_post(self::TEMPLATE_SEND_URL, $data);
        return $result;
    }


}
