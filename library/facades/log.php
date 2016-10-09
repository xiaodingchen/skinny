<?php
/**
 * log.php
 * 
 * late-xiao@foxmail.com
 * */
use Monolog\Logger as Monolog;
use lib_log_writer as Writer;

class lib_facades_log extends lib_facades_facade{
    
    protected static $__log;
    
    protected static function getFacadeAccessor()
    {
        if (!static::$__log)
        {
            static::$__log = new Writer(
                new Monolog(kernel::environment())
            );
            static::configureHandlers(static::$__log);
        }
        
        return static::$__log;
    }
    
    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected static function configureHandlers(Writer $log)
    {
        $method = 'configure'.ucfirst(config::get('log.log')).'Handler';
        static::{$method}($log);
    }
    
    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected static function configureSingleHandler(Writer $log)
    {
        $log->useFiles(DATA_DIR.'/logs/skinny.php', config::get('log.record_level'));
    }
    
    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected static function configureDailyHandler(Writer $log)
    {
        $log->useDailyFiles(DATA_DIR.'/logs/skinny.php', 30, config::get('log.record_level'));
    }
    
    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureSyslogHandler(Writer $log)
    {
        $log->useSyslog('skinny', config::get('log.record_level'));
    }
    
    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected static function configureErrorlogHandler(Writer $log)
    {
        $log->useErrorLog(config::get('log.record_level'));
    }
    
}
