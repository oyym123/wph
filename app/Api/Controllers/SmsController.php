<?php

namespace App\Api\Controllers;

use App\Api\components\WebController;
use App\TaoBaoOpenApi;
use Illuminate\Support\Facades\DB;
use App\Sms;
use App\Helpers\Helper;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class SmsController extends WebController
{

    /**
     * Name: send
     * Desc:
     * User: lixinxin <lixinxinlgm@fangdazhongxin.com>
     * Date: 2017-00-00
     * @param int $isAjax
     * @SWG\Get(path="/demo/demo",
     *   tags={"demo"},
     *   summary="",
     *   description="Author: lixinxin",
     *   @SWG\Parameter(name="mobile", in="query", required=true, type="string", default="1",
     *     description="手机号码"
     *   ),
     *   @SWG\Parameter(name="ky-token", in="isAjax", required=true, type="string", default="1",
     *     description="是否为ajax请求 1 或 0",
     *    ),
     *   @SWG\Response(
     *       response=200,description="
     *          id=1
     *          name=测试"
     *   )
     * )
     */
    public function send($isAjax = 0)
    {
//        $_POST['mobile'] = 18606615070;
        $this->weixinWebOauth(); // 需要网页授权登录
        if (empty($_POST['mobile']) || !Helper::isMobile($_POST['mobile'])) {
            self::showMsg('请填写手机号', -1);
        }

        $key = (string)(rand(100000, 999999)); //获取随机验证码
        DB::beginTransaction(); //开启事务

        try {
            $client = new Client([
                'accessKeyId' => config('aliyun.app_key'),
                'accessKeySecret' => config('aliyun.secret_key'),
            ]);
            $sendSms = new SendSms;
            $sendSms->setPhoneNumbers($_POST['mobile']);
            $sendSms->setSignName('爱谛沉香');
            $sendSms->setTemplateCode('SMS_115925544');
            $sendSms->setTemplateParam(['code' => $key]);
            $sendSms->setOutId('demo');
            $res = $client->execute($sendSms);

            if (isset($res->Message) && $res->Message == 'OK' && isset($res->Code) && $res->Code == 'OK') {   //触发业务流控，后期得改

                $id = (new Sms())->create([
                    'mobile' => $_POST['mobile'],
                    'user_id' => session('user_id'),
                    'type' => Sms::TYPE_USER_REG,
                    'key' => $key,
                    'status' => Sms::STATUS_1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);

                DB::commit();
                //  self::showMsg('短信验证码发送成功', 1);
                echo '短信验证码发送成功';
            } else {
                throw new \Exception('手机格式不正确');
            }
        } catch (\Exception $e) {
            DB::rollback();
            self::showMsg($e->getMessage(), -1);
            echo $e->getMessage();
        }
    }

}
