<?php

return [
    "protocol" => env("YUNLOT_PROTOCOL","v1.0"),
    "encodetype" => env("YUNLOT_ENCODETYPE","1"),//1不加密,2AES加密
    "token" => env("YUNLOT_TOKEN","cloudnetlot"),
    "key" => env("YUNLOT_KEY","abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG"),
    "lottype" => [
    	"up" => "1",
    	"down" => "2"
    ]
];
