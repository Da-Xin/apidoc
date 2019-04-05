ThinkPHP5 文档生成工具

    访问地址:配置的域名/doc?user=user&label=label
        user：用户
        label：用户标识
        user、label 必传且不能为空,且在 doc.php 里的 auth数组中存在,否则无法访问

使用说明

    1.安装：composer require "phpdaxin/apidoc:dev-master"
        更新 composer update phpdaxin/apidoc
        删除 composer remove phpdaxin/apidoc
    
    2.安装完成：
        ThinkPHP5.0 在 application/extra 目录下创建名为 doc.php 的文件;
        ThinkPHP5.1 在 config 目录下创建名为 doc.php 的文件;
        配置文件内容如下：
        ```
        <?php
        return [
            'title' => "接口文档",
            'desc' => [             // 注意事项 或 项目说明
                '注意事项1',
                '注意事项2',
            ],
            'param' => [             // 通用 接收参数
                [
                    'name' => 'token',
                    'type' => 'string',
                    'required' => true,
                    'default' => '',
                    'desc' => '加密参数 字符串且不能为空',
                ]
            ],
            'url' => 'http://www.baidu.com',    // 请求链接
            'class' => [
                'app\index\controller\Index',  // 要生成帮助文档的控制器, 
                ....
            ],
            // 设置可以访问的人员，可以配置多个 user 和 label
            'auth'=>[
                    'user'=>'label',    // user 和 label，就是访问地址里的 user 和 label
                    'zhangsan'=>'zhangsandemo',
            ]
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
             * @title 测试案列
             * @url 连接
             * @request GET
             * @desc  测试案列
             * @param {"name":"file","type":"文件类型","required":true,"default":"","desc":"文件"}
             * @param {"name":"size","type":"int","required":false,"default":"mp4","desc":"大小"}
             * @param {"name":"name","type":"string","required":false,"default":"名称"}
             * @return {"name":"code","type":"int","required":true,"desc":"0：成功 1：失败","level":1}
             * @return {"name":"data","type":"array","required":true,"desc":"返回数据","level":1}
             * @return {"name":"url","type":"string","required":true,"desc":"链接","level":2}
             * @return {"name":"name","type":"string","required":true,"desc":"名称","level":2}
             * @return {"name":"msg","type":"string","required":true,"desc":"失败提示","level":1}
             */
            public function index(){}
            
            /**
             * 有hidden这个函数会隐藏不会展示在接口列表中
             * @hidden
             */
            public function hidden(){}
            ...
        }
        ```