<?php
if (defined('THINK_VERSION') === false && class_exists('\think\facade\Route')) {
    \think\facade\Route::get('doc', "\\PhpApiDoc\\ApiDocSrc\\Doc@get");
    \think\facade\Route::get('getScript', "\\PhpApiDoc\\ApiDocSrc\\Doc@getScript");
} else {
    \think\Route::get('doc', "\\PhpApiDoc\\ApiDocSrc\\Doc@get");
    \think\Route::get('getScript', "\\PhpApiDoc\\ApiDocSrc\\Doc@getScript");
}
