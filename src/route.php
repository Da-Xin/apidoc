<?php
if (defined('THINK_VERSION') === false) {
    Route::get('doc', "\\PhpApiDoc\\ApiDocSrc\\Doc@get");
} else {
    \think\Route::get('doc', "\\PhpApiDoc\\ApiDocSrc\\Doc@get");
}
