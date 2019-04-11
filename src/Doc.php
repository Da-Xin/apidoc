<?php
/**
 * 自动生成文档类
 */

namespace PhpApiDoc\ApiDocSrc;


class Doc
{
    private $view;
    private $config;
    private $request;

    public function __construct()
    {
        if (defined('THINK_VERSION') === false) {
            $this->request = \think\facade\Request::instance();
            $this->view = \think\facade\View::config('view_path', __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
            $this->config = \think\facade\Config::get('doc.');
        } else {
            $this->request = \think\Request::instance();
            $this->view = \think\View::instance(['view_path' => __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR], []);
            $this->config = \think\Config::get('doc');
        }
    }

    /**
     * 接口的列表页
     * @author opqnext
     * @date 2017-8-15
     */
    public function get()
    {
        $input = input('param.');
        $auth = $this->config['auth'];
        if (isset($input['user']) && !empty($input['user']) && isset($auth[$input['user']]) && isset($input['label']) && !empty($input['label']) && $input['label'] == $auth[$input['user']]) {
            $url = $this->config['url'];
            $desc = $this->config['desc'];
            $title = $this->config['title'];
            $class = $this->config['class'];
            $param = $this->config['param'];
            $return = $this->config['return'];
            $ApiDesc = new ApiDesc();
            if (!empty($return) && !empty($return['data'])) {
                foreach ($return['data'] as $key => $item) {
                    $return['data'][$key] = $ApiDesc->getData('', $item);
                }
            }
            $list = $data = [];
            foreach ($class as $val) {
                $methods = $this->getMethods($val, 'public');
                $val_array = explode('\\', $val);
                $class_name = end($val_array);
                // 获取类名称
                $cinfo = $ApiDesc->getDesc($val);

                foreach ($methods as $k => $v) {
                    // 方法文档信息
                    $info = $ApiDesc->getDesc($val, $v);
                    if (!empty($info)) {
                        $info['name'] = $v;
                        $info['class_name'] = $class_name;
                        $info['class_title'] = isset($cinfo['title']) ? $cinfo['title'] : '';
                        $info['interface'] = ucwords($val_array['0']) . '.' . ucwords($val_array['1']) . '.' . $class_name . '.' . ucwords($v);
                        $info['class_path'] = implode('/', $val_array);

                        if (!in_array($info['class_path'], $param['hidden']) && !empty($param['data'])) {
                            $info['param'] = array_merge($info['param'], $param['data']);
                        }
                        if (!in_array($info['class_path'], $return['hidden']) && !empty($return['data'])) {

                            $info['return'] = array_merge($return['data'], $info['return']);
                        }
                        $list[] = $info;
                    }
                }
            }
        } else {
            $title = '没有权限访问';
            $desc = [];
            $list = [];
        }
        $this->view->assign('url', $url);
        $this->view->assign('list', $list);
        $this->view->assign('desc', $desc);
        $this->view->assign('title', $title);
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