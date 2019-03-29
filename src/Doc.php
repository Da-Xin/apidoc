<?php
/**
 * 自动生成文档类
 */

namespace PhpApiDoc\ApiDocSrc;

use think\Config;
use think\Request;
use think\View;

class Doc
{
    private $view;
    private $config;
    private $request;

    private $template = [
        'type' => 'Think',
        'view_path' => '',
        'view_suffix' => 'html',
        'view_depr' => DS,
        'tpl_begin' => '{',
        'tpl_end' => '}',
        'taglib_begin' => '{',
        'taglib_end' => '}',
    ];

    public function __construct()
    {
        $this->request = Request::instance();
        $this->template['view_path'] = __DIR__ . DS . 'view' . DS;
        $this->view = View::instance($this->template, []);
        $this->config = Config::get('doc');
    }

    /**
     * 接口的列表页
     * @author opqnext
     * @date 2017-8-15
     */
    public function get()
    {
        $class = $this->config['class'];
        $list = $data = [];
        foreach ($class as $val) {
            $methods = $this->getMethods($val, 'public');
            $val_array = explode('\\', $val);
            if ($val_array['0'] == 'addons') {
                $url = $val_array['0'] . '/execute/' . $val_array['1'] . '-' . strtolower($val_array['3']) . '-';
            }
            $class_name = end($val_array);
            // 获取类名称
            $cinfo = (new ApiDesc())->getDesc($val);
            foreach ($methods as $k => $v) {
                // 方法文档信息
                $info = (new ApiDesc())->getDesc($val, $v);
                $info['url'] = $url . $v;
                $info['name'] = $v;
                $info['class_name'] = $class_name;
                $info['class_title'] = $cinfo['title'];
                $info['interface'] = ucwords($val_array['0']) . '.' . ucwords($val_array['1']) . '.' . $class_name . '.' . ucwords($v);
                $info['class_path'] = implode('/', $val_array);
                $methods[$k] = $info;
            }
            //            $list[] = [
            //                'title' => $this->Ctitle($val),
            //                'class' => implode('/', $val_array),
            ////                'param' => ,
            //                'class_title' => end($val_array),
            //                'method' => $methods,
            //            ];
        }
        $list = $methods;
//                echo '<pre>';
//                //        print_r($methods);
//                print_r($list);
//                exit;
        $this->view->assign('list', $list);
        $this->view->assign('title', $this->config['title']);
        return $this->view->fetch('doc');
    }

    /**
     * 获取类中非继承方法和重写方法
     * 只获取在本类中声明的方法，包含重写的父类方法，其他继承自父类但未重写的，不获取
     * 例
     * class A{
     *      public function a1(){}
     *      public function a2(){}
     * }
     * class B extends A{
     *      public function b1(){}
     *      public function a1(){}
     * }
     * getMethods('B')返回方法名b1和a1，a2虽然被B继承了，但未重写，故不返回
     * @param string $classname 类名
     * @param string $access public or protected  or private or final 方法的访问权限
     * @return array(methodname=>access)  or array(methodname) 返回数组，如果第二个参数有效，
     * 则返回以方法名为key，访问权限为value的数组
     * @see  使用了命名空间，故在new 时类前加反斜线；如果此此函数不是作为类中方法使用，可能由于权限问题，
     *   只能获得public方法
     */
    public function getMethods($classname, $access = null)
    {
        $class = new \ReflectionClass($classname);
        $methods = $class->getMethods();
        $returnArr = [];
        foreach ($methods as $value) {
            if ($value->class == $classname) {
                if ($access != null) {
                    $methodAccess = new \ReflectionMethod($classname, $value->name);

                    switch ($access) {
                        case 'public':
                            if ($methodAccess->isPublic()) array_push($returnArr, $value->name);
                            break;
                        case 'protected':
                            if ($methodAccess->isProtected()) array_push($returnArr, $value->name);
                            break;
                        case 'private':
                            if ($methodAccess->isPrivate()) array_push($returnArr, $value->name);
                            break;
                        case 'final':
                            if ($methodAccess->isFinal()) $returnArr[$value->name] = 'final';
                            break;
                    }
                } else {
                    array_push($returnArr, $value->name);
                }

            }
        }
        return $returnArr;
    }
}