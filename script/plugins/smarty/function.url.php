<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {url} function plugin
 * Type:     function<br>
 * Name:     url<br>
 * Params:
 * <pre>
 * - action      - name of cycle (optional)
 * - to    - comma separated list of values to cycle, or an array of values to cycle
 *               (this can be left out for subsequent calls)
 * - route     - boolean - resets given var to true
 * </pre>
 * Examples:<br>
 * <pre>
 * {url action=index_ctl_index@index}
 * {url to=test.html}
 * {url route=index.get id=2 t=test}
 * </pre>
 * 生成一个url，action,to,route不可同时使用, 其他的参数是url参数
 
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @return string|null
 */

function smarty_function_url($params, $template)
{
    list($type) = array_keys($params);
    $value = array_shift($params);
    switch ($type)
    {
        case 'action':
            $url = url::action($value, $params);
            break;
        case 'to':
            $url = url::to($value, $params);
            break;
        case 'route':
            $url = url::route($value, $params);
            break;
        default:
            throw new SmartyException($type . ' Not in the middle of [action, to, route]');
    }
    return $url;
}
