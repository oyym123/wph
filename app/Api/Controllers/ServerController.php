<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2017/11/30
 * Time: 12:03
 */

namespace App\Api\Controllers;

use App\Api\components\WebController;
use App\UserInfo;
use TheSeer\Tokenizer\Exception;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Message\News;

class ServerController extends WebController
{

    function __construct()
    {
        $this->weixin = self::weixin();
    }

    function index()
    {
        // 测试
        file_put_contents('/tmp/test.log', var_export($_POST, 1), FILE_APPEND);
        file_put_contents('/tmp/test.log', var_export($_GET, 1), FILE_APPEND);
        file_put_contents('/tmp/test.log', PHP_EOL . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL, FILE_APPEND);
        $this->weixin->server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    if ($message->Event == 'subscribe') {
                        return $this->_subscribe($message);
                    } elseif ($message->Event == 'unsubscribe') {
                        return $this->_unsubscribe($message);
                    } elseif ($message->Event == 'CLICK') {
                        return $this->_click($message);
                    }
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });
        $response = $this->weixin->server->serve();
        // 将响应输出
        return $response;
    }

    private function _subscribe($message)
    {

        //下面是你点击关注时，进行的操作
//        $userInfo['unionid'] = $message->ToUserName;
        $userInfo['openid'] = $message->FromUserName;
        file_put_contents('/tmp/test.log', var_export($message, 1), FILE_APPEND);
        $user = $this->weixin->user->get($userInfo['openid']);
        $userInfo['subscribed_at'] = $user['subscribe_time'];
        $userInfo['unsubscribed_at'] = 0; // 如果之前取消关注,再次关注则去掉取消关注这个值
        $userInfo['nickname'] = $user['nickname'];
        $userInfo['user_photo'] = $user['headimgurl'];
        $userInfo['sex'] = $user['sex'];
        $userInfo['updated_at'] = time();
        $userInfo['address_str'] = $user['province'] . ' ' . $user['city'] . ' ' . $user['country'];

        try {
            $userInfoItem = DB::table('user_info')->where('openid', $userInfo['openid'])->first();

            DB::beginTransaction(); //开启事务
            if ($userInfoItem) {
                // 更新
                $result = DB::table('user_info')
                    ->where('id', $userInfoItem->id)
                    ->update($userInfo);
                if (!$result) {
                    throw new Exception('更新用户出错');
                }
            } else {
                // 新增
                $id = DB::table('users')->insertGetId([
                    'email' => time() . mt_rand(1000000, 9999999) . '@e.com',
                    'name' => md5($userInfo['openid']),
                    'avatar' => 'users/default.png',
                    'password' => md5($userInfo['openid'] . mt_rand(100000, 999999)),
                    'remember_token' => time(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                if (!$id) {
                    throw new Exception('保存用户出错');
                }
                $userInfoId = DB::table('user_info')->insertGetId([
                        'user_id' => $id,
                        'updated_at' => time(),
                        'created_at' => time(),
                    ] + $userInfo);
                if (!$userInfoId) {
                    throw new Exception('保存用户信息出错');
                }
                $userInfoItem = DB::table('user_info')->where('id', $userInfoId)->first();
            }

            if ($userInfoItem) {
                (new UserInfo())->inviteBind($userInfoItem, $message);
            }

            DB::commit();


            // return "您好！欢迎关注爱谛沉香!";
        } catch (Exception $e) {
            DB::rollback();

            return '您的信息由于某种原因没有保存，请重新关注';
        }

        $this->weixin->server->setMessageHandler(function () {
            $news1 = new News([
                'title' => '德才师父视频',
                'description' => "欣赏德才师父四十二手眼玫瑰掌内功精彩视频请点此注册。(如已注册，则直接进入视频中心)",
                'url' => config('app.url') . 'api/article',
                'image' => 'https://mmbiz.qpic.cn/mmbiz_png/16rcM8fWf9bL4rozl7Ix2gsS45HGqewwibwKStKzicUIlPtTKX1zOggJ32tXialgRbogsIFfOHw94SJQMtQAmicehQ/640?wx_fmt=png&wxfrom=5&wx_lazy=1',
            ]);
            return [$news1];
        });
        return $this->weixin->server->serve()->send();
    }

    private function _unsubscribe($message)
    {
        DB::table('user_info')
            ->where('openid', $message->FromUserName)
            ->update([
                'unsubscribed_at' => time()
            ]);
    }

    /** 菜单栏响应 */
    private function _click($message)
    {
        switch ($message->EventKey) {
            case '11': // 集团介绍
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '集团简介',
                        'description' => '爱谛（中国）控股集团是一家集国内外沉香艺术收藏、文化传播、专利研发、生态种植、生产销售为一体的现代化高新科技企业',
                        'url' => 'https://mp.weixin.qq.com/s/6cRQzXp-hS2fDDhwh2CmhA',
                        'image' => 'http://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9YKibq5Q3l8qsju2lPg9icAWmxyjKZEm0NmoEmNyFygpcQX54XMvSnz4sd6pSbp33vqQRDsMzatyg6Q/640?wx_fmt=jpeg&wxfrom=5&wx_lazy=1',
                    ]);
                    $news2 = new News([
                        'title' => '集团创始人介绍（德才师父）',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/GAwzSn-xANA3PufSHXaLXA',
                        'image' => 'https://mmbiz.qlogo.cn/mmbiz_jpg/16rcM8fWf9Y1tkah6n7Ow2zQIsl1DF6j37cibY8uicky4zsCpBuwRNuwvKRRaCpqkmTwDY7S0oagNBKhdrslQx4w/0?wx_fmt=jpeg',
                    ]);
                    return [$news1, $news2];
                });
                $this->weixin->server->serve()->send();
                break;
            case '12': // 集团新闻
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '爱谛祝福-中秋快乐',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/l-Cle-6NoB9ymAWlBK5YbA',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9ZZ0icFGmt0uRk223KKWicZibuTjkH7pbZDX0Bb7NtHnCfWicHzJibFYMs1HW2tQXmnpIxYVSx2IVeNB0Q/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news2 = new News([
                        'title' => '让健康的生命美丽绽放—爱谛沉香为大健康助力',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/eO0vBi_j9Ix2W4BgX3rFUA',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9Y9BwudZeXuNgwz78VIVg93MSStkVKyzPJuCC89bhy2n0lUfKtUPpnvJy2Ms2x9Azv2fKTSLJxC9g/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news3 = new News([
                        'title' => '古老而神奇的沉香文化—将在美丽富饶的无锡发扬光大！',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/nbwOK-qleiyga6yImstO8g',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9YXlXX1PGtENgx1c4WL8xIDj1WGyicXev7RbTmEc0jjHZ7tOj80Hf8W7Qq17gq0WbJ7PjGQotVzTOQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news4 = new News([
                        'title' => '知产链上线发布会于杭州举办 共话区块链与文化知识产业未来',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/MA2PL71lHdDC27fN2c1fqw',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9ZjEo2eZ543WSOmkBb2osJntD0EUFn054IVltYQSeSg01qM513v5OVicR390NBGp9Y2ibicsXxELGBVQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news5 = new News([
                        'title' => '记联合国NGO峰会中国代表团精彩瞬间',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/FZDF2nSqvD0VE-UiQTKgRQ',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_png/16rcM8fWf9btLcHD6rqiaF7QtT4FicQyftYWeJiaTXBYQibXByDSkxCHm6xZLa604msBkCXuMcxz0ZpSUlpYYgqLuw/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    return [$news1, $news2, $news3, $news4, $news5];
                });
                $this->weixin->server->serve()->send();
                break;

            case '13': // 沉香生态产业园
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '沉香生态产业园',
                        'description' => '沉香生态产业园将规划建设成为产品优质化、品牌知名度高、科技支撑有力、经济效益显著、园区环境优美、勇攀现代高新技术高峰的云南十强现代高新科技园区。成为云南重要的沉香高新技术种植、生产基地，成为带动农村经济发展农民增收的龙头企业，成为云南十佳生态示范园区，同时为全省现代高新科技园区建设探索新思路、新模式和新经验。',
                        'url' => 'https://mp.weixin.qq.com/s/4lGjc7P7IwF7Ibon2UeuKQ',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9btLcHD6rqiaF7QtT4FicQyftNvBc8icthCQ5KaARrW4icWo12IaaNrSm0MPH9H43tic2QA1WN0hpicBqaA/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    return [$news1];
                });
                $this->weixin->server->serve()->send();
                break;
            case '14': // 沉香文化
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '沉香之于风水裨益',
                        'description' => '沉香在风水应用上有极其重要之地位，沉香相纯阳，味能通三界；用于风水能冲阴合阳而后成化生机，如太极之理可品语化机之妙。以沉香树或沉香摆件之阳与周围自然环境相呼应生，清静家宅，令阴不生聚;顾守家园而聚气生财，助旺磁场，驱蚊驱虫，能使居者身心健康，增添福寿。',
                        'url' => 'https://mp.weixin.qq.com/s/9_szY_sFVuSyqjoM-t_PTw',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9ag6S6sFZWPX662UeEVfibuZ00E7sXdjcZoicBB7yBW81RdDWy2nCBhK8m3mpkscSiale421vXVWmRIw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news2 = new News([
                        'title' => '沉香与沉香木,一字差千金',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/igb5HyJayLhQVUIaUmte5w',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9YicIlp2YmJ8icIibpaOJBK5S15ictNUApIoQ5gXzCGg3PJ9evewCfLS9vcZ59z0aSLKlNoguiciawyfDfg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news3 = new News([
                        'title' => '沉香的分类及印尼沉香的特点',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/KRcb0yYzpuEeefjK0L7cyg',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9b6IDhZuszmZTyibkIqnUk12o0pFtRZ9y3PUicrWKtZKM1dzRiamHzBFBCsbWA7HicHvcXOuYHG9s7ricQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news4 = new News([
                        'title' => '沉香比其他宝石更具有收藏价值',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/TCPS5qnwg-JN86Pp2-7vVQ',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9aGtywY2ZWnF0ffGfFBXJCfIxxZ7iaml6qZI4o0tzpX1kVJPsEg0QKMqbuY580f6Q9O98ibLtLkweQw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news5 = new News([
                        'title' => '沉香的十大价值',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/E3KFJurkrJZXMi3OTIBimg',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9ZsGbzF3NicIqmqIzywnI8tuABWhjwKYKEToyhLWIEZNSnEabWKHZewv78xRY1epbYw4baTDqyWTBA/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news6 = new News([
                        'title' => '什么是沉香？沉香的形成过程与分类？',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/d3Kdj0Nj42uzcHVaNst88g',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9b2LRPibUT6oibF28u9CqzDwYV2UJEpib4FmXhWnseZF9CfK6oZWZtuawqaPxDyoJuxPcD10Aq1V9reQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news7 = new News([
                        'title' => '沉香文化',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/xjdgt3ncG3oTLRoaEfW6Ow',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9btLcHD6rqiaF7QtT4FicQyftVj2MZApFkFKTxXpvPPP431jKp7lvDfVfb1YZvFsaTcbuAfseAYEYmA/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);

                    return [$news1, $news2, $news3, $news4, $news5, $news6, $news7];
                });
                $this->weixin->server->serve()->send();
                break;
            case '15': // 诚聘英才
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '商务合作',
                        'description' => '',
                        'url' => 'https://mp.weixin.qq.com/s/q3NkY0WPXW1gHOzdVubPLA',
                        'image' => 'https://mmbiz.qlogo.cn/mmbiz_jpg/16rcM8fWf9YRFYylaib5ORSJibZ3jic4uicRG1r1dJQp4cKh6wm17Q0iaxB3JO7jAGDViaIAZGC24ASzP31MXBzLumcQ/0?wx_fmt=jpeg',
                    ]);
                    $news2 = new News([
                        'title' => '招聘',
                        'description' => '',
                        'url' => 'http://s.wcd.im/v/195opZ4h/',
                        'image' => '',
                    ]);
                    return [$news1, $news2];
                });
                $this->weixin->server->serve()->send();
                break;
            case '21': // 沉香烟宝
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '沉香烟宝',
                        'description' => '爱谛沉香烟宝是微排行独家发明拥有的专利产品，是以原产纯正印度尼西亚巴布亚岛之千年原生态极品精细沉香粉，应用印尼特有配方技术精心加工调制而成的世界独一无二的沉香香烟伴侣精品，该产品已荣获中国国家发明专利并已成功投放市场。',
                        'url' => 'https://mp.weixin.qq.com/s/f2RtWieBR1TLfs88dShFSw',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_png/16rcM8fWf9YzuKPMre32FIkvj1ZJ49TXHiaGmsTRdYmH6k64icyxHxe19Wicpq9kxcAzMOhPs8h2kDHicLNw4UFicvg/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    $news2 = new News([
                        'title' => '沉香雪茄',
                        'description' => '爱谛沉香雪茄是以原产印度尼西亚巴布亚岛之极品沉香及古巴上乘烟叶为原料，经获得中国发明专利的印尼配方技术精心调制发酵，并由古巴资深卷烟师手工精心巻制而成的世界独一无二的沉香雪茄精品。',
                        'url' => 'https://mp.weixin.qq.com/s/_sdp7eVY_eSZSUbZfhfMAg',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_png/16rcM8fWf9btLcHD6rqiaF7QtT4FicQyftQ7aEficcicgZ7UudfAxRkEKibpsXEIwojUiaibvPdiaDwjW6Z5Q9UlqiaYuZw/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    return [$news1, $news2];
                });
                $this->weixin->server->serve()->send();
                break;
            case '22': // 沉香养生酒
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '沉香养生酒',
                        'description' => '爱谛沉香养生酒，精选印尼巴布亚千年沉香老料、顶级玛咖并配以国酒茅台品质级白酒原浆、近30年全封闭式特殊工艺窖藏，终酿成极品沉香养生酒，成为国内独一无二的沉香养生保健酒。爱谛沉香养生保健酒香气宜人、口感绵柔具有舒筋活血、赠强肾上腺皮质功能，能促进身体机能新陈代谢、改善体内自由基代谢，温补肾阳、强筋健骨、增强体力，男女皆宜。',
                        'url' => 'https://mp.weixin.qq.com/s/OsYwQXzevoAFfd2ameOLVw',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9bL4rozl7Ix2gsS45HGqeww0KUIjzFnmDtehcDQNcHgQYPvXarSdo8aYA5dUzg4bbAPCkzRySoA5Q/640?wx_fmt=jpeg&wxfrom=5&wx_lazy=1',
                    ]);
                    return [$news1];
                });
                $this->weixin->server->serve()->send();
                break;
            case '23': // 沉香女性用品
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '沉香女性用品',
                        'description' => '一、沉香面膜 爱谛沉香面膜是由新加坡爱谛国际集团研发技术，在中国申请获得发明专利的特色美容产品(专利号201510802857.X)。二、沉香女性私密护理湿巾 爱谛沉香女性私密护理湿巾是新加坡爱谛国际集团女性私护系列产品中最受欢迎的一款功效卓越的畅销产品（中国发明专利号：2010102715693）',
                        'url' => 'https://mp.weixin.qq.com/s/yoB7HHDgWol5aAQo6IX8MA',
                        'image' => 'https://mmbiz.qpic.cn/mmbiz_jpg/16rcM8fWf9YRFYylaib5ORSJibZ3jic4uicRRvPkGVIkDWI7qM3KIHhibEoibf5VeIdJ2pDIHwGa9vZUicqsmU8thjL8A/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                    ]);
                    return [$news1];
                });
                $this->weixin->server->serve()->send();
                break;
            case '24': // 沉香禅茶
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '沉香禅茶',
                        'description' => '据说在乾隆十一年，和珅、纪晓岚陪同乾隆微服私访云南时，发生了意外，乾隆被绑匪绑架，扣为人质，乾隆爷几日滴水未进，身体状况十分堪忧，衰老明显。然而就在乾隆被救出后,下人呈上一碗茶给乾隆，谁曾料想乾隆爷饮茶后气色渐好，衰老的面容也恢复了往日的光彩。乾隆大喜，遂问此茶来历，才知道这神奇的茶就是“沉香普洱茶”。',
                        'url' => 'https://mp.weixin.qq.com/s/QdnRZbtlDV1vcjEQ3MZTow',
                        'image' => 'https://mmbiz.qlogo.cn/mmbiz_jpg/16rcM8fWf9btLcHD6rqiaF7QtT4FicQyft4WthwIBeE4969fufko03flIpYGib5XMM2aps6Q9JSh9LjBgc7Rq5J9w/0?wx_fmt=jpeg',
                    ]);
                    return [$news1];
                });
                $this->weixin->server->serve()->send();
                break;
            case '25': // 其他产品
                $this->weixin->server->setMessageHandler(function () {
                    $news1 = new News([
                        'title' => '香薰产品',
                        'description' => '香熏的习俗在我国由来已久，其始于三国两晋并一直延绵至清末民初。香熏亦称香炉、熏笼，是古人用于薰香取暖、洁室、杀虫、清洁衣被的卫生用具。其质地有金、银、铜、瓷、陶等多种，造型及工艺多较考究。一般场合下，古人是把香料制成饼块，放在特制的香炉内焚烧的，最早的香炉叫“博山香炉”，后来又被叫做“宝鼎”。  ',
                        'url' => 'https://mp.weixin.qq.com/s/GV5wlVass7mCKfIlMrBU3g',
                        'image' => 'https://mmbiz.qlogo.cn/mmbiz_jpg/16rcM8fWf9btLcHD6rqiaF7QtT4FicQyfticawv9TiaZXOQjmwrTW72Iv89LWY1h7Ey88BAknAyQLThtW1eQaONc2w/0?wx_fmt=jpeg',
                    ]);
                    $news2 = new News([
                        'title' => '沉香猫屎咖啡',
                        'description' => '印尼巴布亚《爱谛沉香猫屎咖啡》，是选用印尼巴布亚六种独特的黑棕色沉香，用传统的手工成粉，加手工炒印尼苏门答腊岛亚齐曼得宁独有的麝香猫屎咖啡豆，本咖啡是世界级咖啡豆的极品，浓稠度糖浆般的润滑口感，不涩、不酸、不苦，香味可谓“独家”，口间还会留有淡淡的薄荷糖香、梨花香、桃花香、柠檬草、细腻花香、蜜瓜、雪梨、丝丝白酒香感',
                        'url' => 'https://mp.weixin.qq.com/s/mf-PGkZO6WF74vTAZ2kqyg',
                        'image' => 'https://mmbiz.qlogo.cn/mmbiz_jpg/16rcM8fWf9bL4rozl7Ix2gsS45HGqewwGAw50ZRxJp9cKbzhdsRib73LicGrLZ6K2rUJYvrBTJh5iarKRhtHhUHiaQ/0?wx_fmt=jpeg',
                    ]);
                    return [$news1, $news2];
                });
                $this->weixin->server->serve()->send();
                break;
            case '33': // 礼品兑换
                return '功能开发中哦,敬请期待!';
                break;
            default:
                return '功能开发中哦...';
                break;
        }

    }

    function test()
    {

        // 测试积分
        DB::beginTransaction(); //开启事务
        try {
            $userInfoItem = DB::table('user_info')->where('user_id', 7)->first();

            (new UserInfo())->bindMobileAndAwardInviter($userInfoItem);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        DB::commit();
        exit;

    }
}