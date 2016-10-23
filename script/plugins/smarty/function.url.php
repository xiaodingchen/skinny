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

function smarty_function_url($params)
{
    logger::info('test:'.json_encode($params));

    return '111';
}
