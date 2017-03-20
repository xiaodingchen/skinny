<?php 
/**
 * 加载css文件
 */
function smarty_function_css($params, $tpl)
{
    $appname = empty($params['app']) ? 'common' : $params['app'];
    $src = $params['src'];
    $real_path = sprintf('%s/%s/css/%s', '/static', $appname, $src);

    return '<link rel="stylesheet" type="text/css" href="'.$real_path.'" />';
}
