<?php

namespace PhpApiDoc\ApiDocSrc;

class ApiDesc
{
    /**
     * 获取指定文件 文档信息
     * @param string $class 文件路径
     * @param string $method 方法名
     * @return array|bool
     * @throws \ReflectionException
     */
    public function getDesc($class, $method = '')
    {
        if (empty($method)) {
            $rs = new \ReflectionClass($class);
        } else {
            $rs = new \ReflectionMethod($class, $method);
        }
        $res = explode("\n", $rs->getDocComment());
        $res = $this->setDocComment($res, $method);
        return $res;
    }

    /**
     * 设置 文档信息
     * @param $res
     * @param $method
     * @return array|bool
     */
    private function setDocComment($res, $method)
    {
        $result = [];
        if (is_array($res)) {
            $doc_tag = $this->getDocTag();
            foreach ($res as $v) {
                $pos = 0;
                $content = "";
                preg_match("/@[a-z]*/i", $v, $tag);
                if (isset($tag[0]) && array_key_exists($tag[0], $doc_tag)) {
                    if ($tag[0] == '@hidden') {
                        return [];
                    }
                    $pos = stripos($v, $tag[0]) + strlen($tag[0]);
                    if ($pos > 0) {
                        $content = trim(substr($v, $pos));
                    }
                    if ($content && ($tag[0] == '@param' || $tag[0] == '@return' || $tag[0] == '@header')) {
                        $result[strtr($tag[0], ["@" => ''])][] = $this->getData($tag[0], $content);
                    } else {
                        if (isset($doc_tag[$tag[0]]) && empty($content)) {
                            $content = $doc_tag[$tag[0]];
                        }
                        $result[strtr($tag[0], ["@" => ''])] = str_replace('\\', '', $content);
                    }
                }
            }
            if (!empty($method)) {
                $result = array_merge($result, array_diff_key($this->getDocDefault(), $result));
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 获取 param/return 数据
     * @param $key
     * @param $item
     * @return array
     */
    public function getData($key, $item)
    {
        //过滤传入参数
        if (!is_array($item)) {
            $item = (array)json_decode($item);
        }
        if (!isset($item['name'])) {
            $item['name'] = '';
        }
        if (isset($item['level']) && !empty($item['level']) && !empty($item['name']) && $item['level'] > 1) {
            $item['name'] = str_repeat('&emsp;&emsp;', ($item['level'] - 1)) . $item['name'];
        }

        if (!isset($item['type'])) {
            $item['type'] = 'string';
        }
        $dataType = $this->getDataType();
        if (isset($dataType[$item['type']])) {
            $item['type'] = $dataType[$item['type']];
        }

        if (!isset($item['required'])) {
            $item['request'] = false;
        } else {
            $item['request'] = $item['required'];
        }

        if (!isset($item['desc'])) {
            $item['desc'] = '';
        }
        $item['desc'] = trim($item['desc']);
        if ($key == '@param' || $key == '@header') {
            if (!isset($item['default'])) {
                $item['default'] = '';
            }
            if ($item['default'] === null) {
                $item['default'] = 'NULL';
            } elseif (is_array($item['default'])) {
                $item['default'] = json_encode($item['default']);
            } elseif (!is_string($item['default'])) {
                $item['default'] = var_export($item['default'], true);
            }
        }
        return $item;
    }

    /**
     * 获取 没个接口默认值
     * @return array
     */
    public function getDocDefault()
    {
        return ['title' => '未配置', 'request' => '未配置', 'url' => '未配置', 'desc' => '未配置', 'header' => [], 'param' => [], 'return' => []];
    }

    /**
     * 获取 文件扩展
     * @param $ext
     * @return mixed|string
     */
    public function getExtInfo($ext)
    {
        $exts = [
            'xml' => 'application/xml,text/xml,application/x-xml',
            'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
            'js' => 'text/javascript,application/javascript,application/x-javascript',
            'css' => 'text/css',
            'rss' => 'application/rss+xml',
            'yaml' => 'application/x-yaml,text/yaml',
            'atom' => 'application/atom+xml',
            'pdf' => 'application/pdf',
            'text' => 'text/plain',
            'png' => 'image/png',
            'jpg' => 'image/jpg,image/jpeg,image/pjpeg',
            'gif' => 'image/gif',
            'csv' => 'text/csv',
            'html' => 'text/html,application/xhtml+xml,*/*',
        ];
        if (isset($exts[$ext]) && !empty($exts[$ext])) {
            return $exts[$ext];
        }
        return 'text/html';
    }

    /**
     * 获取 数据类型
     * @return array
     */
    private function getDataType()
    {
        return [
            'string' => '字符串', 'int' => '整型', 'float' => '浮点型', 'boolean' => '布尔型', 'date' => '日期',
            'array' => '数组', 'fixed' => '固定值', 'enum' => '枚举类型', 'object' => '对象',
        ];
    }

    /**
     * 获取 文档标示
     * @return array
     */
    private function getDocTag()
    {
        return [
            '@example' => '例子', '@return' => '返回值', '@param' => '参数', '@version' => '版本信息',
            '@throws' => '抛出的错误异常', '@title' => '标题', '@desc' => '描述', '@request' => '请求方式',
            '@url' => '请求链接', '@hidden' => '隐藏', '@header' => '请求头'
        ];
    }
}
