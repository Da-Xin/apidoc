<?php

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
     *  接口的列表页
     * @return mixed
     * @throws \ReflectionException
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
            if (!empty($return) && isset($return['data']) && !empty($return['data'])) {
                foreach ($return['data'] as $key => $item) {
                    $return['data'][$key] = $ApiDesc->getData('', $item);
                }
            }
            $list = $data = [];
            foreach ($class as $val) {
                $methods = $this->getMethods($val, ['public']);
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
                        if (!isset($param['hidden'])) {
                            $param['hidden'] = [];
                        }
                        if (!isset($return['hidden'])) {
                            $return['hidden'] = [];
                        }
                        if (isset($param['data']) && !empty($param['data']) && !in_array($info['class_path'], $param['hidden']) && !in_array($info['class_path'] . '/' . $info['name'], $param['hidden'])) {
                            $info['param'] = array_merge($info['param'], $param['data']);
                        }
                        if (isset($return['data']) && !empty($return['data']) && !in_array($info['class_path'], $return['hidden']) && !in_array($info['class_path'] . '/' . $info['name'], $return['hidden'])) {
                            $info['return'] = array_merge($return['data'], $info['return']);
                        }
                        $list[] = $info;
                    }
                }
            }
        } else {
            $url = '';
            $title = '没有权限访问';
            $desc = [];
            $list = [];
        }
        $this->view->assign('title', $title);
        $this->view->assign('doc_info', ['url' => $url, 'list' => $list, 'desc' => $desc]);
        return $this->view->fetch('doc');
    }


    /**
     * @param $classname            列名称
     * @param array $access 访问形式数组 如：['public'，'protected'，'private'，'abstract','constructor','destructor','final','static']
     *      abstract:抽象方法, constructor:构造方法, destructor:析构方法, final:定义final, private:私有方法, protected:保护方法
     *      public:公开方法, static:静态方法
     * @return mixed
     * @throws \ReflectionException
     */
    public function getMethods($classname, $access = ['public'])
    {
        $class = new \ReflectionClass($classname);
        $methods = $class->getMethods();
        $search = array_column($methods, 'class');
        $data = [];
        while (($key = array_search($classname, $search)) !== false) {
            if (in_array('public', $access) && $methods[$key]->isPublic()) {
                array_push($data, $methods[$key]->getName());
            } elseif (in_array('protected', $access) && $methods[$key]->isProtected()) {
                array_push($data, $methods[$key]->getName());
            } elseif (in_array('private', $access) && $methods[$key]->isPrivate()) {
                array_push($data, $methods[$key]->getName());
            } elseif (in_array('final', $access) && $methods[$key]->isFinal()) {
                $data[$methods[$key]->getName()] = 'final';
            }
            unset($search[$key]);
        }
        return $data;
    }
}