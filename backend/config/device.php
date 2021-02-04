<?php

return [
    "producttype" => [
    	"ROUTE" => "1",//路由器
    ],
    'typeinfo' => [
		'encode' => '1',
		'system' => '2',
		'network' => '3',
		'wifi' => '4',
		'user' => '5',
		'time_reboot' => '6',
		'upgrade' => '7',
		'bind' => '8',
		'upinfofail' => '9',
		'up' => '10'
	],
	'status' => [
		'online' => "1",
		'offline' => "0"
	],
	'wifi' => [
		'support' => [
			'country_code' => [
				['code' => 'CN','channel' => [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]]
			],
			'encode' => [1,2,3,4,5,6,7,8,9],
			'phymode' => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
			'power' => ['12.5','25','50','75','100']
		]
	]
];
