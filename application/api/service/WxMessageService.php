<?php
namespace app\api\service;


class WxMessageService {
    private $sendUrl = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token=%s';
    private $touser;
    //不让子类控制颜色
    private $color = 'black';

    protected $tplID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyWord;

    function __construct()
    {
        $access_token = new AccessToken();
        $token = $access_token->get();
        // sprintf 把百分号（%）符号替换成一个作为参数进行传递的变量
        $this->sendUrl = sprintf($this->sendUrl, $token);
    }

    /**
     *
     * 发送模板消息
     * @return bool 发送结果
     */
    protected function sendMessage($openid)
    {
        $data = [
            'touser' => $openid,
            'template_id' => $this->tplID,
            'page' => $this->page,
            'form_id' => $this->formID,
            'data' => $this->data,
            'emphasis_keyword' => $this->emphasisKeyWord
        ];
        $result = curl_post($this->sendUrl,$data);
        $result = json_decode($result, true);
        if($result['errcode'] != 0) throw new Exception('模板消息发送失败，'.$result['errmsg']);

        return true;
    }
}
