<?php
namespace App;

use Illuminate\Support\Facades\DB;

class Invite extends Base
{
    protected $table = 'invite';
    const TYPE_LEVEL_FIRST = 1; //等级1
    const TYPE_LEVEL_SECOND = 2; //等级2

    /**
     * 插入绑定的数据
     * Auth:oyym
     * Date:2017/12/8
     */
    public static function inviteCreate($inviteMobile, $userId)
    {
        DB::beginTransaction();
        try {
            $level_1 = 0;
            $level_2 = 0;
            $userInfoId = 0;
            $leve1InfoId = 0;

            if ($inviteMobile != '') { //邀请的字符串不为空的话，说明有推荐人
                $inviteInfo = DB::table('user_info')->where('bind_mobile', $inviteMobile)->first();//查询出一级推荐人的信息
                if (!$inviteInfo) {
                    throw new \Exception("推荐人不存在");
                }
                //通过一级推荐人查询出推荐二级推荐人user_id
                $level1 = DB::table('invite')->where('user_id', $inviteInfo->user_id)->first();
                if (!$level1) {
                    throw new \Exception("推荐人信息不在推荐表中，（异常）");
                }
                //正常情况下都能反向查询到上级，但是异常的话将设置为空值，统计的数量也将不会增加
                $leve1Info = DB::table('user_info')->where('user_id', $level1->level_1)->first();

                //throw new \Exception("$level1->user_id");

                if (!$level1->level_1 && $inviteInfo) { //二级推荐人为空，一级推荐人存在

                    $userInfoId = $inviteInfo->id; //一级推荐人user_info的id主键

                    $level_1 = $inviteInfo->user_id; //将一级推荐人的user_id赋值

                    $level_2 = 0;
                } elseif ($level1->level_1 && $inviteInfo) {//都存在
                    $leve1InfoId = $leve1Info->id; //二级推荐人user_info的id主键
                    $userInfoId = $inviteInfo->id;
                    $level_1 = $inviteInfo->user_id;
                    $level_2 = $level1->level_1;
                }
//保存invite
                $invite = DB::table('invite')->where('user_id', $userId)->first();
                if (!$invite) {
                    $model = new Invite();
                    $model->level_1 = $level_1;
                    $model->level_2 = $level_2;
                    $model->user_id = $userId;
                    if (!$model->save()) {
                        throw new \Exception("保存失败");
                    }
                }
            } else {
                //保存invite
                $invite = DB::table('invite')->where('user_id', $userId)->first();
                if (!$invite) {
                    $model = new Invite();
                    $model->level_1 = $level_1;
                    $model->level_2 = $level_2;
                    $model->user_id = $userId;
                    if (!$model->save()) {
                        throw new \Exception("保存失败");
                    }
                }
            }

            //throw new \Exception("$userInfoId");
            if ($inviteMobile != '') {
                if ($level_1 && $level_2) {
                    //更新一级推荐人信息
                    UserInfo::updateInfo(['id' => $userInfoId, 'invite_level1_count' => self::inviteCount('level_1', $level_1)]);
                    UserInfo::updateInfo(['id' => $leve1InfoId, 'invite_level2_count' => self::inviteCount('level_2', $level_2)]);
                } elseif ($level_1 && !$level_2) {
                    //更新二级推荐人信息
                    UserInfo::updateInfo(['id' => $userInfoId, 'invite_level2_count' => self::inviteCount('level_1', $level_1)]);
                }
            }
            DB::commit();
            return ['保存成功', 1];
        } catch (\Exception $e) {
            DB::rollback();
            return [$e->getMessage(), 0];
        }
    }

    /** 用户推荐的人数 */
    public static function inviteCount($level, $userId)
    {
        return DB::table('invite')
            ->where("$level", $userId)
            ->where('user_id', '>', 0)
            ->count();
    }
}