<?php
namespace App\Providers;

use Illuminate\Log\LogServiceProvider as Base;
use Illuminate\Log\Writer;

class LogServiceProvider extends Base{

	/**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureSingleHandler(Writer $log){
    	$config = $this->app->make("config");
    	$fileName = $config->get("app.log_path",$this->app->storagePath() . "/logs/") . $config->get("app.log_name","laravel") . ".log";
        $log->useFiles(
            $fileName,
            $this->logLevel()
        );
    }

	/**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureDailyHandler(Writer $log){
    	$config = $this->app->make("config");
    	$fileName = $config->get("app.log_path",$this->app->storagePath() . "/logs/") . $config->get("app.log_name","laravel") . ".log";
        $log->useDailyFiles(
            $fileName,
            $this->maxFiles(),
            $this->logLevel()
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureSyslogHandler(Writer $log){
        $log->useSyslog($this->app->make("config")->get("app.log_name","laravel"), $this->logLevel());
    }
}