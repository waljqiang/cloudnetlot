<?php
return [
	"options" => [
		"address" => env("MQ_ADDRESS","127.0.0.1"),
		"port" => env("MQ_PORT",1883),
		"username" => env("MQ_USERNAME",""),
		"password" => env("MQ_PASSWORD",""),
		"clean" => env("MQ_CLEAN",true),
		"qos" => env("MQ_QOS",0),
		"keepalive" => env("MQ_KEEPALIVE",10),
		"timeout" => env("MQ_TIMEOUT",30),
		"retain" => env("MQ_RETAIN",0),
	],
	"topic" => [
		"deviceup" => "+/+/dev2app",
		"devicedown" => "+/+/app2dev",
		"online" => "\$SYS/brokers/+/clients/+/connected",
		"offline" => "\$SYS/brokers/+/clients/+/disconnected"
	],
];