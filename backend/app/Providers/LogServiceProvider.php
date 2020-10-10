<?php
namespace App\Providers;

use Illuminate\Log\LogServiceProvider as Base;
use Illuminate\Log\Writer;
use Monolog\Logger as Monolog;

class LogServiceProvider extends Base{

    /**
     * Create the logger.
     *
     * @return \Illuminate\Log\Writer
     */
    public function createLogger(){
        $timeZone = env("APP_TIMEZONE","");
        if(!empty($timeZone))
            Monolog::setTimezone(new \DateTimeZone($timeZone));
        $log = new Writer(
            new Monolog($this->channel()), $this->app['events']
        );

        if ($this->app->hasMonologConfigurator()) {
            call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
        } else {
            $this->configureHandler($log);
        }

        return $log;
    }

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