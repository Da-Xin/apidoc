ThinkPHP5 文档生成工具

使用说明

    1.安装：composer require "phpdaxin/apidoc:dev-master"
    
    2.安装完成：
        ThinkPHP5.0 在 application/extra 目录下创建名为 doc.php 的文件;
        ThinkPHP5.1 在 config 目录下创建名为 doc.php 的文件;
        配置文件内容如下：
        ```
        <?php
        return [
            'title' => "接口文档",
            'class' => [
                'app\index\controller\Index',  // 要生成帮助文档的控制器, 
                ....
            ],
        ];
        ```

    3.配置文件配置完成：
        编写生成帮助文档注释
        案列如下(app\index\controller\Index)：
        ```
        <?php
        namespace app\index\controller
        /**
         * @title 测试案列
         */
        class Index extends Controller
        {
            /**
             * @title 测试案列函数
             * @request GET
             * @desc  方法详细信息说明
             * @param {"name":"demo","type":"int","required":true,"default":"1","desc":"测试参数"}
             * @return {"name":"status","type":"int","required":true,"desc":"测试参数返回值","level":1}
             * @return {"name":"message","type":"string","required":true,"desc":"返回信息","level":1}
             * @return {"name":"data","type":"array","required":true,"desc":"返回数据","level":1}
             * @return {"name":"id","type":"string","required":true,"desc":"文章ID(22位字符串)","level":2}
             * @return {"name":"title","type":"string","required":true,"desc":"文章标题","level":2}
             * @return {"name":"thumb","type":"string","required":true,"desc":"文章列表图","level":2}
             * @return {"name":"content","type":"text","required":true,"desc":"文章内容","level":2}
             * @return {"name":"cate","type":"int","required":true,"desc":"文章分类","level":2}
             * @return {"name":"tags","type":"array","required":true,"desc":"文章标签","level":2}
             * @return {"name":"id","type":"string","required":true,"desc":"标签ID","level":3}
             * @return {"name":"tag","type":"string","required":true,"desc":"标签名称","level":3}
             * @return {"name":"count","type":"int","required":true,"desc":"标签使用数","level":3}
             * @return {"name":"img","type":"array","required":true,"desc":"文章组图","level":2}
             */
            public function index(){}
            ...
        }
        ```