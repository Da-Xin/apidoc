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
                    $pos = stripos($v, $tag[0]) + strlen($tag[0]);
                    if ($pos > 0) {
                        $content = trim(substr($v, $pos));
                    }
                    if ($content && ($tag[0] == '@param' || $tag[0] == '@return')) {
                        $result[strtr($tag[0], ["@" => ''])][] = $this->getData($tag[0], $content);
                    } else {
                        if (isset($doc_tag[$tag[0]]) && empty($content)) {
                            $content = $doc_tag[$tag[0]];
                        }
                        $result[strtr($tag[0], ["@" => ''])] = $content;
                    }
                }
            }
            if (!empty($method)) {
                $result = array_merge($result, array_diff_key(['title' => '未配置', 'request' => '未配置', 'url' => '未配置', 'desc' => '未配置', 'param' => [], 'return' => []], $result));
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
    private function getData($key, $item)
    {
        //过滤传入参数
        $item = (array)json_decode($item);
        if (!isset($item['name'])) {
            $item['name'] = '';
        }
        if (isset($item['level']) && !empty($item['level']) && !empty($item['name'])) {
            $item['name'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $item['level']) . $item['name'];
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

        if ($key == '@param') {
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
     * 获取 数据类型
     * @return array
     */
    public function getDataType()
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
    public function getDocTag()
    {
        return [
            '@example' => '例子', '@return' => '返回值', '@param' => '参数', '@version' => '版本信息',
            '@throws' => '抛出的错误异常', '@title' => '标题', '@desc' => '描述', '@request' => '请求方式',
            '@url' => '请求链接'
        ];
    }
}