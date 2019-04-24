<?php
if (defined('THINK_VERSION') === false && class_exists('\think\facade\Route')) {
    \think\facade\Route::get('doc', "\\PhpApiDoc\\ApiDocSrc\\Doc@get");
} else {
    \think\Route::get('doc', "\\PhpApiDoc\\ApiDocSrc\\Doc@get");
}
