<?php

namespace App\Api\Controllers;

use App\Api\components\WebController;

class SwaggerController extends WebController
{
    /**
     * 返回JSON格式的Swagger定义
     *
     * 这里需要一个主`Swagger`定义：
     * @SWG\Swagger(
     *   @SWG\Info(
     *     title="我的`Swagger`API文档",
     *     version="1.0.0"
     *   )
     * )
     */
    public function getJSON()
    {
        // 你可以将API的`Swagger Annotation`写在实现API的代码旁，从而方便维护，
        // `swagger-php`会扫描你定义的目录，自动合并所有定义。这里我们直接用`Controller/`
        // 文件夹。
        $swagger = \Swagger\scan(app_path('Api/Controllers/'));

        return response()->json($swagger, 200);
    }

    /**
     * /**
     * @SWG\Get(path="/api/swagger/my-data",
     *   tags={"demo"},
     *   summary="",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function getMyData()
    {
        self::showMsg('12321');
    }
}
