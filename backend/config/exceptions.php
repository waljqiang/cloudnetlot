<?php
/**
 * 
 * 
 */
return [
    //system
    "SUCCESS" => 10000,//成功
    "ERROR" => 10001,//失败

    //common
    "HTTP_REQUEST_NO_EXISTS" => 600000100,//请求不存在
    "HTTP_NO_ALLOWED_METHOD" => 600000101,//请求方法不允许
    "THROTTLE_ERROR" => 600000102,//请求过于频繁,
    "LIMIT_ERROR" => 600000103,//接口调用次数超过限制

    //auth
    "LICENSE_NO" => 600100100,//没有license
    "LICENSE_INVALID" => 600100101,//license invalid
    "LICENSE_EXPIRE_IN" => 600100102,//license expired
    "TOKEN_NO" => 600100103,//token必须
    "TOKEN_INVALID" => 600100104,//token无效
    "TOKEN_EXPIRES" => 600100105,//token过期
    "AUTH_NO" => 600100106,//未认证,包含token无效或过期

    "REFRESH_TOKEN_REQUIRED" => 600100107,//refresh_token必须
    "REFRESH_TOKEN_INVALID" => 600100108,//refresh_token无效
    "REFRESH_TOKEN_EXPIRES" => 600100109,//refresh_token过期

    //mqtt
    "MQTT_RECEIVE_ERROR" => 600101100,//mqtt监听回调错误
    "MQTT_CONNECT_ERROR" => 600101101,//mqtt连接失败
    "MQTT_PUBLISH_ERROR" => 600101102,//发布消息失败

    //mysql
    "MYSQL_EXEC_ERROR" => 600102100,//mysql语句执行错误

    //file
    "FILE_PUT_ERROR" => 600103100,//写文件失败

    //redis
    "REDIS_CONNECT_ERROR" => 600104100,//redis连接异常

    //validator
    "PARAMS_INVALID" => 600400100,//参数无效
    "COMPANY_REQUIRED" => 600400101,//公司名称必须
    "COMPANY_DOMAIN_REQUIRED" => 600400102,//公司服务器地址必须
    "LICENSE_EXPIRE_IN_REQUIRED" => 600400103,//license过期时间必须
    "LICENSE_EXPIRE_IN_NUMERIC" => 600400104,//license过期时间必须为数字
    "CAPTCHA_REQUIRED" => 600400105,//验证码必须
    "CAPTCHA_INVALID" => 600400106,//验证码无效
    "USER_PASSWORD_ERROR" => 600400107,//用户名或密码错误
    "USER_STATUS_NO_ALLOWED" => 600400108,//用户状态不允许
    "USER_LEVEL_NO_ALLOWED" => 600400109,//用户级别不允许
    "USER_EXISTS" => 600400110,//账号已存在
    "USER_NICKNAME_REGEX" => 600400111,//用户昵称不合法
    "USER_USERNAME_REQUIRED" => 600400112,//账号必须
    "USER_USERNAME_ALPHA_DASH" => 600400113,//账号必须是字母、数字、下划线、破折号
    "USER_USERNAME_BETWEEN" => 600400114,//账号必须为3-20个
    "USER_PASSWORD_REQUIRED" => 600400115,//,密码必须
    "USER_PASSWORD_ALPHA_DASH" => 600400116,//密码必须是字母、数字、下划线、破折号
    "USER_PASSWORD_BETWEEN" => 600400117,//密码必须为6-20个
    "USER_PASSWORD_CONFIRMED" => 600400118,//确认密码必须
    
    "EMAIL" => 600400119,//邮箱不合法
    "ADDRESS_MAX" => 600400120,//详细地址过长
    "AREA_EXISTS" => 600400121,//不支持的区域码
    "COUNTRY_PHONECODE_EXISTS" => 600400122,//不支持的国家区域码
    "PHONE" => 600400123,//手机号格式不正确
    "MAC_REGEX" => 600400124,//MAC格式错误
    "MAC_REQUIRED" => 600400125,//设备MAC必须
    "DEV_TYPE_REQUIRED" => 600400126,//设备型号必须
    "EMAIL_REQUIRED" => 600400127,//邮箱必须
    "ADDRESS_REQUIRED" => 600400128,//详细地址必须
    "USER_NO_EXISTS" => 600400129,//用户不存在
    "USER_EMAIL_ERROR" => 600400130,//用户邮箱错误
    "URL_INVALID" => 600400131,//链接无效
    "URL_EXPIRE" => 600400132,//链接过期
    "COUNTRY_PHONECODE_REQUIRED" => 600400133,//国家区域码必须
    "PHONE_REQUIRED" => 600400134,//手机号必须
    "PHONE_PHONECODE_REQUIRED" => 600400135,//手机号存在时必须有国家区域码
    "USER_PASSWORD_CONFIRMED" => 600400136,//两次密码不一致
    "USER_PASSWORD_DIFFERENT" => 600400137,//新密码不能与旧密码一样
    "USER_PASSWORD_ERROR" => 600400138,//密码错误
    "FILE_FILE" => 600400139,//文件上传不成功
    "FILE_FILEEXT" => 600400140,//不支持的文件
    "FTP_UPLOAD_FAILURE" => 6004001141,//FTP上传文件失败
    "USER_NO_WORKGROUP" => 600400142,//没有此工作组
    "NO_PERMISSTION" => 600400143,//没有权限
    "MAX_DEPTH" => 600400144,//已经到最大层级
    "GROUP_NAME_REQUIRED" => 600400145,//工作组名称必须
    "GROUP_NAME_INVALID" => 600400146,//工作组名称不合法
    "GROUP_DESC_MAX" => 600400147,//工作组描述长度最多为100
    "AUTO_WITH_CONFIG" => 600400148,//auto字段存在必须有config_id字段
    "AUTO_IN" => 600400149,//auto取值只能为0、1
    "PAGEINDEX_NUMERIC" => 600400150,//pageIndex必须是数字
    "PAGEOFFSET_NUMERIC" => 600400151,//pageOffset必须是数字
    "GID_REQUIRED" => 600400152,//工作组ID必须
    "TREE_IN" => 600400153,//tree取值只能为0,1
    "WORKGROUP_ROOT_NO" => 600400154,//根工作组不能修改或删除
    "WORKGROUP_BELONGS_CHILD" => 600400155,//有子账号拥有此工作组
    "WORKGROUP_HAS_CHILD" => 600400156,//工作组有子工作组
    "WORKGROUP_HAS_DEVICE" => 600400157,//工作组中不能有设备
    "USER_ROLE_REQUIRED" => 600400158,//用户角色必须
    "USER_ROLE_IN" => 600400159,//用户角色值必须为1或2
    "WORKGROUP_GIDS_ARRAY" => 600400160,//工作组IDs必须为数组
    "WORKGROUP_GIDS_DISTINCT" => 600400161,//工作组IDs不能重复
    "WORKGROUP_NODE_NO_TREE" => 600400162,//工作组节点不是一个完整的树结构
    "ENABLE_REQUIRED" => 600400163,//enable必须
    "ENABLE_IN" => 600400164,//enable必须为0或1
    "UNSUPPORT_SORTKEY" => 600400165,//不支持的排序key
    "UNSUPPORT_SORT" => 600400166,//不支持的排序方法
    "USER_UID_REQURIED" => 600400167,//用户ID必须
    "USER_UID_ARRAY" => 600400168,//用户ID必须为数组
    "STATUS_IN" => 600400169,//状态必须为0,1,2,3,4
    "DATE_FORMAT" => 600400170,//日期格式不正确
    "ID_ARRAY" => 600400171,//ID必须是数组
    "ID_DISTINCT" => 600400172,//ID不能重复
    "COMM_NO" => 600400173,//命令不存在
    "DEV_NO_CONNECT" => 600400174,//设备没有连接云平台
    "DEV_USERNAME_REQUIRED" => 600400175,//设备账号必须
    "DEV_PASSWORD_REQUIRED" => 600400176,//设备密码必须
    "BINDCODE_ERROR" => 600400177,//绑定码错误
    "DEV_BINDED" => 600400178,//设备已绑定其他用户
    "NO_STATUS_IN" => 600400179,//不支持的设备状态
    "DEV_TYPEINFO_REQUIRED" => 600400180,//设备信息类型必须
    "DEV_TYPEINFO_ARRAY" => 600400181,//设备信息类型必须是数组
    "DEV_TYPEINFO_IN" => 600400182,//不支持的设备信息类型
    "MACS_ARRAY" => 600400183,//设备MACS必须是数组
    "DEVICE_NO_USER" => 600400184,//有不属于您的设备
    "DEVICE_NO" => 600400185,//设备不存在
    "DEVICE_STATUS_NO_ALLOWED" => 600400186,//设备状态不允许
    "MAC_DISTINCT" => 600400187,//设备MAC不能重复
    "START_REQUIRED" => 600400188,//起始日期必须
    "DATE_NO" => 600400189,//非日期格式
    "DATE_START_LET_END" => 600400190,//起始日期必须小于终止日期
    "END_REQUIRED" => 600400191,//终止日期必须
    "ID_REQUIRED" => 600400192,//ID必须
    "USER_STATUS_IN" => 600400193,//账号状态不符合规则
    "RADIO_REQUIRED" => 600400194,//radio必须
    "RADIO_NUMERIC" => 600400195,//radio必须是数字
    "VAP_REQUIRED" => 600400196,//vap必须
    "VAP_NUMERIC" => 600400197,//vap必须是数字
    "VAP_OPTIONS_REQUIRED" => 600400198,//无线参数必须
    "VAP_SSID_INVALID" => 600400199,//无线ssid不符合规则
    "VAP_ENABLE" => 600400199,//是否启用无线值只能为0或1
    "UN_SUPPORT_CHANNEL" => 600400200,//不支持的信道
    "VAP_ENCODE_IN" => 600400201,//不支持的加密方式
    "VAP_PASSWORD_INVALID" => 600400202,//无线密码不符合规则
    "VAP_TIMER_ENABLE_IN" => 600400203,//无线定时关闭开关仅支持0或1
    "VAP_TIMER_START_END_REQUIRED_IF" => 600400204,//当无线定时开关打开时，起始时间和终止时间必须有
    "VAP_TIMER_START_END_INVALID" => 600400205,//起始时间或终止时间无效
    "VAP_PHYMODE_INVALID" => 600400206,//无线模式无效
    "VAP_USER_ISOLATE_IN" => 600400207,//是否开启用户隔离值必须为0或1
    "VAP_SHORTGI_IN" => 600400208,//是否开启shortgi值必须为0或1
    "VAP_FRAG_THRESHOLD_INVALID" => 600400209,//分片门限值无效
    "VAP_RTS_THRESHOLD_INFVALID" => 600400210,//RTS门限值无效
    "VAP_BEACON_INTERVAL_INVALID" => 600400211,//信号帧间隔无效
    "WIFI_RADIO_NO" => 600400212,//无线radio不存在
    "RADIO_VAP_NO" => 600400213,//无线radio中vap不存在
    "VAP_HIDE_SSID_IN" => 600400214,//是否隐藏ssid值必须为0或1
    "VAP_DISTINCT" => 600400215,//vap不能重复
    "UN_SUPPORT_BANDWIDTH" => 600400216,//不支持的信道带宽
    "UN_SUPPORT_ENCODE" => 600400217,//不支持的加密方式
    "UN_SUPPORT_PHYMODE" => 600400218,//不支持的无线模式
    "TIME_REQUIRED_IF" => 600400219,//当enable为1时，time必须
    "TIME_REGEX" => 600400220,//不支持的time格式
    "DEV_PID_REQUIRED" => 600400221,//父设备MAC必须
    "DEV_NAME_REGEX" => 600400222,//设备名称不符合规则,字母数字中文下划线中横线1-10位
    "IS_VIRTURE_REQUIRED" => 600400223,//is_virture必须
    "IS_VIRTURE_IN" => 600400224,//is_virture必须是0或1
    "DEV_MODE_REQUIRED" => 600400225,//设备工作模式必须
    "DEV_MODE_IN" => 600400226,//设备工作模式必须为0,1,2,3,4,100,101,102,103,104",//0 网关 1中继 2 WISP 3 AP 4 WDS,100：网络根节点,101:交换机,102:摄像头,103:移动设备,104：PC
    "DEV_STATUS_REQUIRED" => 600400227,//设备状态必须
    "DEVICE_POINT_REQUIRED" => 600400228,//坐标必须
    "DEVICE_POINT_NUMERIC" => 600400229,//坐标必须为数字
    "TOPGRAPHY_DATA_NO_TREE" => 600400230,//拓扑图数据不是完整树结构
    "TOPGRAPHY_DATA_ROOT_INVALID" => 600400231,//拓扑图根节点数据无效
    "DEV_NAME_REQUIRED" => 600400232,//设备名称必须
    "DEV_NAME_REGEX" => 600400233,//设备名称不符合规则
    "FILE_EXISTS" => 600400234,//文件已存在
    "FILE_NO" => 600400235,//文件不存在
    "FID_REQURIED" => 600400236,//文件id必须
    "FIDS_ARRAY" => 600400237,//文件IDS必须是数组
    "FID_DISTINCT" => 600400238,//文件ID不能重复
    "DATE_AFTER_NOW" => 600400239,//时间必须晚于现在
    "DEV_TYPE_UNSUPPORT" => 600400240,//不支持的设备型号
    "COMMAND_RETRY" => 600400241,//命令从新执行次数已达最大限制

    "NAME_REQUIRED" => 600500100,//姓名必须
    "NAME_MAX" => 600500101,//姓名过长
    "IDCARD_REQUIRED" => 600500102,//身份码必须
    "IDCARD_MAX" => 600500103,//身份码过长
    "ENTERPRISE_REQUIRED" => 600500104,//企业名称必须
    "ENTERPRISE_MAX" => 600500105,//企业名称过长
    "ENTERPRISE_DES_REQUIRED" => 600500106,//企业描述必须
    "ENTERPRISE_DES_MAX" => 600500107,//企业描述过长
    "ENTERPRISE_CODE_REQUIRED" => 600500108,//企业社会信用码必须
    "ENTERPRISE_MAX" => 600500109,//企业社会信用码过长
    "USER_DEVELOPING" => 600500110,//已经提交申请
    "USER_DEVELOPED" => 600500111,//已经是开发者
    "USER_NO_AGREE" => 600500112,//有不在审批范围内的用户
    "LANG_IN" => 600500113,//不支持的语言
    "IDCARD_ALPHA_NUM" => 600500114,//身份码必须是字母或数字
    "PRT_NAME_REQUIRED" => 600500115,//产品名称必须
    "PRT_NAME_REGEX" => 600500116,//产品名称不合法
    "PRT_TYPE_REQUIRED" => 600500117,//产品类型必须
    "PRT_TYPE_NO" => 600500118,//不支持的产品类型
    "PRT_SIZE_REQUIRED" => 600500119,//产品型号必须
    "PRT_SIZE_REGEX" => 600500120,//产品型号不合法
    "PRT_DES_REQUIRED" => 600500121,//产品描述必须
    "PRT_DES_MAX" => 600500122,//产品描述过长
    "PRT_REGISTERED" => 600500123,//产品已经注册
    "PRT_ID_REQUIRED" => 600500124,//产品ID必须
    "PRT_NO" => 600500125,//产品不存在
    "PRT_STATUS_NO_ALLOW" => 600500126,//产品状态不允许
    "APPID_REQUIRED" => 600500127,//APPID必须
    "APP_SECRET_REQUIRED" => 600500128,//secret必须
    "APPID_OR_SERCRET_ERROR" => 600500129,//appid or secret error
    "CLT_ERROR" => 600500130,//客户端id错误
    "PRTID_ERROR" => 600500131,//产品ID错误
];
