<?php 
/**
 * 加载js文件
 */
function smarty_function_script($params, $tpl)
{
    $appname = empty($params['app']) ? 'common' : $params['app'];
    $src = $params['src'];
    $real_path = sprintf('%s/%s/js/%s', '/static', $appname, $src);

    return '<script type="text/javascript" src="'. $real_path .'"></script>';
}
