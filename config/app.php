<?php
/**
 * app.php
 * app配置文件
 * */
return [
    /*
     |--------------------------------------------------------------------------
     | System Debug Mode
     |--------------------------------------------------------------------------
     |
     | 当开启调试模式, 详细的错误会暴露出来, 否则会提示错误页
     | 对应原系统: DEBUG_PHP + DEBUG_CSS + DEBUG+JS
     |
     */
    'debug' => true,
    
    /*
     |--------------------------------------------------------------------------
     | System Url
     |--------------------------------------------------------------------------
     |
     | This URL is used by the console to properly generate URLs when using
     | the command line tool. You should set this to the root of
     | your application so that it is used when running system tasks.
     | 对应原系统: BASE_URL
     |
     */
    'url' => '%URL%',
    
    /*
     |--------------------------------------------------------------------------
     | Application Timezone
     |--------------------------------------------------------------------------
     |
     | Here you may specify the default timezone for your application, which
     | will be used by the PHP date and date-time functions. We have gone
     | ahead and set this to a sensible default for you out of the box.
     | 对应原系统:DEFAULT_TIMEZONE
     |
     */
    
    'timezone' => '%TIMEZONE%',
    
    /*
     |--------------------------------------------------------------------------
     | Application Locale Configuration
     |--------------------------------------------------------------------------
     |
     | The application locale determines the default locale that will be used
     | by the translation service provider. You are free to set this value
     | to any of the locales which will be supported by the application.
     | 对应原系统: LANG
     |
     */
    'locale' => 'zh_CN',
];
