<?php
/**
 * command.php
 *
 * 执行命令
 * */
class lib_command_command{
    
    public function __construct()
    {
        set_time_limit(0);
        $timezone = config::get('app.timezone', 8);
        date_default_timezone_set('Etc/GMT' . ($timezone >= 0 ? ($timezone * - 1) : '+' . ($timezone * - 1)));
    }
    
    public function run()
    {
        ignore_user_abort(false);
        ob_implicit_flush(1);
        ini_set('implicit_flush', true);
        if (strpos(strtolower(PHP_OS), 'win') === 0)
        {
            if (function_exists('mb_internal_encoding'))
            {
                mb_internal_encoding("UTF-8");
                mb_http_output("GBK");
                ob_start("mb_output_handler", 2);
            }
            elseif (function_exists('iconv_set_encoding'))
            {
                iconv_set_encoding("internal_encoding", "UTF-8");
                iconv_set_encoding("output_encoding", "GBK");
                ob_start("ob_iconv_handler", 2);
            }
        }
        
        if (isset($_SERVER ['argv'] [1]))
        {
            // 开始处理自定义的命令了
            $command = $_SERVER ['argv'] [1];
            array_shift($_SERVER['argv']);
            array_shift($_SERVER['argv']);
            $commandArgs = $_SERVER['argv'];
            
            $this->exec($command, $commandArgs);
        }
        else
        {
            $this->interactive();
        }
    }
    
    // 客户端交互
    public function interactive()
    {
        $this->print_welcome();
        $i = 1;
        while (true)
        {
            // code...
            $line = readline("\n" . $i ++ . '>');
            readline_add_history($line);
            $this->exec($line);
        }
    }
    
    /**
     * 执行命令
     * 
     * @param string $command 命令
     * @param array $args 命令参数
     * @return void
     * */
    public function exec($command, array $args = [])
    {
        $command = trim($command);
        if ($command == '')
        {
            echo "please input command";
        }else{
            // 如果输入的命令以;号结尾，那就作为php执行
            if(substr($command, -1, 1) == ';')
            {
                $this->phpCall($command);
            }else{
                // 执行自定义的命令
                $commandClassName = $this->checkCommand($command);
                
                logger::info('Scanning local Applications...');
                $commandObj = new $commandClassName();
                if($commandObj instanceof lib_command_interface)
                {
                    $commandObj->handle($args);
                }
                else
                {
                    logger::info("Error: {$commandClassName}  must implement the lib_command_interface interface.");
                    
                    exit;
                }
                
            }
        }
    
    }
    
    /**
     * 验证command是否注册
     * 
     * @param string $command
     * @return string $commandClassName
     * */
    public function checkCommand($command)
    {
        $commands = config::get('command', []);
        $commands = array_merge($this->getDefaultDefineCommand(), $commands);
        
        if($command && array_key_exists($command, $commands))
        {
            return $commands[$command];
        }
        
        logger::info($command . ':Command not defined.');
        exit;
    }
    
    public function getDefaultDefineCommand()
    {
        return [
            'help'=>'lib_command_set_help',
            'list'=>'lib_command_set_list',
        ];
    }
    
    // 处理简单的php语句
    public function phpCall()
    {
        $this->output(eval(func_get_arg(0)));
    }
    
    // 处理输出
    public function output()
    {
        $args = func_get_args();
        foreach($args as $data){
            switch(gettype($data)){
                case 'object':
                    echo 'Object<'.get_class($data).">\n";
                    break;
    
                case 'integer':
                case 'double':
                case 'resource':
                case 'string':
                    echo $data;
                    break;
    
                case 'array':
                    print_r($data);
    
                default:
                    var_dump($data);
            }
        }
    }
    
    public function print_welcome()
    {
        echo "skinny shell (abort with ^C), Root: ",ROOT_DIR;
    }
}
