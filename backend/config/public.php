<?php

return [
    "default" => [
        "admin" => env("APP_DEFAULT_ADMIN","cloudnetlot"),
        "password" => env("APP_DEFAULT_PASSWORD","123456"),
        "timeZone" => env("APP_DEFAULT_TIMEZONE","+08:00"),
        "isSummerTime" => env("APP_DEFAULT_ISSUMMERTIME","0"),
        "area" => env("APP_DEFAULT_AREA","610113"),
        "address" => env("APP_DEFAULT_ADDRESS","锦业路"),
        "lat" => env("APP_DEFAULT_LAT","34.243172"),
        "lng" => env("APP_DEFAULT_LNG","108.888119"),
        "phonecode" => env("APP_DEFAULT_PHONECODE","86"),
        "phone" => env("APP_DEFAULT_PHONE","12345678911"),
        "email" => env("APP_DEFAULT_EMAIL","waljqiang@163.com"),
        "country" => env("APP_DEFAULT_COUNTRY","CN"),
    ],
    "user" => [
        "status" => [
        	"enabled" => 1, 
        	"disabled" => 0
        ],
        "level" => [
            "child_guest" => 1,//具备guest权限的子账号
            "child_admin" => 2,//具备admin权限的子账号
            "normal" => 3,//普通账号
        ]
    ],
    "cache" => [
        "registerttl" => 3600*24*30,//1个月过期
    ],
    "pageIndex" => 1,
    "pageOffset" => 10,
    "mailtopassword" => [//找回密码邮件
        "expire_in" => 600,//过期时间10分钟
        "trans" => [
            "zh-cn" => [
                "subject" => "CloudNetLot-找回密码",
                "lang1" => "尊敬的",
                "lang2" => "您正在进行找回密码操作,请点击以下链接修改密码,如果点击此链接并未进行跳转,请尝试复制此链接到浏览器打开.请勿将此链接转发他人,该链接有效期为%s分钟。",
                "lang3" => "本邮件由CloudnetLot平台自动发出,请勿直接回复。",
            ],
            "en-us" => [
                "subject" => "CloudNetLot Platform-find password",
                "lang1" => "Dear ",
                "lang2" => "You are in the process of retrieving your password. Please click on the link below to modify your password. If you click on this link without jumping, please try to copy this link to the browser to open it. Do not forward this link to anyone else. The link is valid for %s minutes.",
                "lang3" => "This email is sent automatically by the CloudNetLot Platform. Please do not reply directly.",
            ],
        ]
    ],
    "mailtoadminwithdevelop" => [//申请开发者邮件
        "trans" => [
            "zh-cn" => [
                "subject" => "CloudNetLot Develop-申请成为开发者",
                "lang1" => "尊敬的 ",
                "lang2" => "用户[%s]申请成为CloudNetLot开发者，请及时审批！",
                "lang3" => "本邮件由CloudNetLot Develop平台自动发出，请勿直接回复。"
            ],
            "en-us" => [
                "subject" => "CloudNetLot Develop-apply to be a developer",
                "lang1" => "Dear ",
                "lang2" => "The user[%s] submit the application for becoming a developer,please approve it in time!",
                "lang3" => "This email is sent automatically by the CloudNetLot Develop. Please do not reply directly.",
            ]
        ]
    ],
    "mailtodevelop" => [//回复申请开发者邮件
        "trans" => [
            "zh-cn" => [
                "subject" => "CloudNetLot Admin-审批开发者申请",
                "lang1" => "尊敬的 %s",
                "lang2" => "您提交的开发者申请已通过，appid为%s,secret为%s,请保存！",
                "lang3" => "您提交的开发者申请未通过！",
                "lang4" => "本邮件由CloudNetLot管理平台自动发出，请勿直接回复。"
            ],
            "en-us" => [
                "subject" => "CloudNetLot Admin-Approve developer application",
                "lang1" => "Dear %s",
                "lang2" => "The developer application you submitted has passed,appid is %s,secret is %s,Please keep it!",
                "lang3" => "The developer application you submitted failed!",
                "lang4" => "This email is sent automatically by the CloudNetLot Admin. Please do not reply directly."
            ]
        ]
    ],
    "mailtoadminwithprtpub" => [//申请产品发布邮件
        "trans" => [
            "zh-cn" => [
                "subject" => "CloudNetLot Develop-申请产品发布",
                "lang1" => "尊敬的 ",
                "lang2" => "用户[%s]申请产品[%s]发布上线，请及时审批！",
                "lang3" => "本邮件由CloudNetLot Develop平台自动发出，请勿直接回复。"
            ],
            "en-us" => [
                "subject" => "CloudNetLot Develop-apply to publish product",
                "lang1" => "Dear ",
                "lang2" => "The user[%s] submit the application for the product[%s] publishing,please approve it in time!",
                "lang3" => "This email is sent automatically by the CloudNetLot Develop. Please do not reply directly.",
            ]
        ]
    ],
    "mailtodevelopwithprtpub" => [//回复申请产品发布邮件
        "trans" => [
            "zh-cn" => [
                "subject" => "CloudNetLot Admin-审批产品发布申请",
                "lang1" => "尊敬的 %s",
                "lang2" => "您提交的产品[%s]发布申请已通过！",
                "lang3" => "您提交的产品[%s]发布申请未通过！",
                "lang4" => "本邮件由CloudNetLot管理平台自动发出，请勿直接回复。"
            ],
            "en-us" => [
                "subject" => "CloudNetLot Admin-Approve publish product",
                "lang1" => "Dear %s",
                "lang2" => "The publish of the product[%s] you submitted has passed!",
                "lang3" => "The publish of the product[%s] you submitted failed!",
                "lang4" => "This email is sent automatically by the CloudNetLot Admin. Please do not reply directly."
            ]
        ]
    ],
    "workgroup" => [
        "default" => [
            "pid" => 0,
            "code" => 0,
            "name" => "我的工作组#My Workgroup",
            "description" => "系统默认工作组,用户不能编辑和删除#The default workgroup of the system,You cant not edit and delete",
        ],
        "level" => 5,
    ],
];
