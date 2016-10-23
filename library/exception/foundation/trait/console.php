<?php
/**
 * console.php
 * 
 * */
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Application as ConsoleApplication;

trait lib_exception_foundation_trait_console
{

    public function renderForConsole(Exception $e)
    {
        $output = new ConsoleOutput();
        (new ConsoleApplication())->renderException($e, $output);
    }
}
