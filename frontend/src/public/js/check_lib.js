let reg_map = {
    login_form: [],

}

function check_input(key, min, max) { 
    var page_map = reg_map[key];
    for (var i in page_map) {
        var reg_val = page_map[i].val
        if (reg_val == '') {
            if (page_map[i].type.indexOf("noneed") != -1) {
                continue;
            }
        }
        var types = page_map[i].type.split(' ');
        for (var p in types) {
            if (types[p] == "noneed")
                continue;
            var reg_type = types[p];
            var res = check_map[reg_type](reg_val);
                
            if (res != true) {          
                return res;
            }
        }
    }
    return true;
}

const checkObj = {
    check_int(str) {//检查整数
        if (str == "" || str == null) {
            var ss = "non_null_integer";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_numeric_char";
                return ss;
            }
        }
        return true;
    },    
    check_decimal(str) {//检查小数
        if (str == "" || str == null) {
            var ss = "non_null_decimal";
            return ss;
        }
        var cmp = '0123456789.';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_decimal_char";
                return ss;
            }
        }
        if (str.split(".")[0] == '' || str.split(".").length > 2 || str.split(".")[1] == '') {
            var ss = "digital_format_incorrect";
            return ss;
        }
        return true;
    },
    check_string_blank(str) {//基类
        if (str == "" || str == null) {
            var ss = "non_null_string";
            return ss;
        }
        return true;
    },
    check_string(str) {
        if (str == "" || str == null) {
            var ss = "non_null_string";
            return ss;
        }
        var cmp = '\\\'"<> ';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) >= 0) {
                var ss = "not_illegal_char";
                return ss;
            }
        }
        return true;
    },
    check_blank(str) {
        if (str == "" || str == null) {        
            return false;
        }
        return true;
    },
    check_int_letter(str) { //不能含有字母数字以外的字符
        if (str == "" || str == null) {
            var ss = "non_null_string";
            return ss;
        }
        var cmp = '0123456789abcdefghijklmnopqrstuvwxyz' + 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_alphanumeric_char";
                return ss;
            }
        }
        return true;
    },
    check_account(str,action){
        let name_reg = /^[\a-\z\A-\Z0-9\-\_]{3,20}$/;
        if(!name_reg.test(str)){
            if(action=='login'){
                return "account_format_error";
            }else{
                return "account_tips";
            }
            
        }
        return true;
    },
    check_nickname(str){
        let name_reg = /^[\a-\z\A-\Z0-9\u4E00-\u9FA5\-\_]{1,20}$/;
        if(str!=""&&!name_reg.test(str)){
            return "nickname_tips";
        }
        return true;
    },
    check_phone(str){
        let phone_reg = /^[0-9]{6,25}$/;
        if(!phone_reg.test(str)){
            return "phone_set_error_tips";
        }
        return true;
    },
    check_email(str){
        let email_reg = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/;
        if(!email_reg.test(str)){
            return "email_set_error_tips";
        }
        return true;
    },
    check_user_pwd(str,action){
        let pwd_reg = /^[\a-\z\A-\Z0-9]{6,20}$/;
        if(!pwd_reg.test(str)){
            if(action=='login'){
                return "pwd_format_error";
            }
            return "pwd_set_error_tips";
        }
        return true;
    },
    check_password(str) {
        if (str == "" || str == null) {
            var ss = "pwd_not_empty";
            return ss;
        }
        var cmp = '\\\'"<>';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            /*if (cmp.indexOf(tst) >= 0) {
                var ss = L.not_illegal_char + cmp;
                return ss;
            }*/
            if (tst.charCodeAt(0) < 0 || tst.charCodeAt(0) > 255) {
                var ss = "not_pwd_chinese";
                return ss;
            }
        }
        return true;
    },
    //检查ip合法性
    check_ip_come(str, check_lan_ip_b) {
        var flg = 0;
        if (str == "") {
            var ss = "ip_not_null";
            return ss;
        }
        for (var h = 0; h < str.length; h++) {
            cmp = "0123456789.";
            var tst = str.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                flg++;
            }
        }
        if (flg != 0) {
            var ss = "ip_incorrect";
            return ss;
        }
        var str2 = str.split(".");
        if (str2.length != 4) {
            var ss = "ip_incorrect_len";
            return ss;
        }
        for (var h = 0; h < str2.length; h++) {
            if (str2[h] == "") {
                var ss = "ip_incorrect";
                return ss;
            }
            if (str2[h] > 255 || str2[h] < 0) {
                var ss = "ip_range_tip";
                return ss;
            }
        }
        if (str2[0] == 0) {
            var ss = "firsr_section_not_zero";
            return ss;
        }
        if (str2[3] == "0") {
            var ss = "four_section_not_zero";
            return ss;
        }
        if (str2[0] == 1 && str2[1] == 0 && str2[2] == 0 && str2[3] == 0) {
            var ss = "ip_incorrect";
            return ss;
        }
        if (str2[0] == 255 && str2[1] == 255 && str2[2] == 255 && str2[3] == 255) {
            var ss = "ip_incorrect";
            return ss;
        }
        if (str2[0] == 127) {
            var ss = "not_loopback_addr";
            return ss;
        }
        if (str2[0] >= 224 && str2[0] <= 239) {
            var ss = "not_multicast_addr";
            return ss;
        }
        if (str2[0] >= 240) {
            var ss = "ip_reserve_addr";
            return ss;
        }
        if (check_lan_ip_b) {
            if (str == ROUTE_INFO.lan_ip) {
                var ss = "not_lan_ip_addr";
                return ss;
            }
        }
        if (str == ROUTE_INFO.lan_mask) {
            var ss = "not_lan_mask_addr";
            return ss;
        }
        var lanIpArray = ROUTE_INFO.lan_ip.split(".");
        var maskArray = ROUTE_INFO.lan_mask.split(".");
        var andIp255 = "", andIp0 = "";
        for (var i = 0; i < 4; i++) {
            var ipItem = (lanIpArray[i] * 1) & (maskArray[i] * 1);
            andIp255 += ipItem ? ipItem : 255;
            andIp0 += ipItem;
            if (i != 3) {
                andIp0 += ".";
                andIp255 += ".";
            }
        }
        if (str == andIp255) {

            var ss = "ip_broadcast_addr";
            return ss;
        }
        if (str == andIp0) {
            var ss = "ip_network_addr";
            return ss;
        }
        return true;
    },
    check_dns(str) {    //检查dns合法性
        if (str == "") {
            var ss = "dns_not_null";
            return ss;
        }
        var reg = /^(|((22[0-3])|(2[0-1]\d)|(1\d\d)|([1-9]\d)|[1-9])(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3})$/;
        flag = reg.test(str);
        if (!flag) {
            var ss = "dns_format_incorrect";
            return ss;
        }
        var str2 = str.split(".");
        if (str2[0] == 127 || str2[3] == 0) {
            var ss = "dns_format_incorrect";
            return ss;
        }
        return true;
    },
    check_port(str) {    //检查端口
        if (str == "" || str == null) {
            var ss = "port_not_null";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "port_non_numeric_char";
                return ss;
            }
        }
        if (parseInt(str, 10) > 65535 || parseInt(str, 10) < 1) {
            var ss = "port_range"; 
            return ss;
        }
        return true;
    },
    check_mask(str) {    //检查mask是否合法
        var strsub = str.split(".");
        if (str == "" || str == "0.0.0.0" || strsub.length != 4 || str == "255.255.255.255") {
            var ss = "mask_err";
            return ss;
        }
        for (var j = 0; j < strsub.length; j++) {
            strsub[j] = parseInt(strsub[j], 10);
            if (strsub[j] != 0 && strsub[j] != 128 && strsub[j] != 192 && strsub[j] != 224 && strsub[j] != 240 && strsub[j] != 248 &&
                strsub[j] != 252 && strsub[j] != 254 && strsub[j] != 255) {
                var ss = "mask_err";
                return ss;
            }
        }
        if (parseInt(strsub[0], 10) != 255 && parseInt(strsub[1], 10) != 0) {
            var ss = "mask_format_err";
            return ss;
        }
        if (parseInt(strsub[1], 10) != 255 && parseInt(strsub[2], 10) != 0) {
            var ss = "mask_format_err";
            return ss;
        }
        if (parseInt(strsub[2], 10) != 255 && parseInt(strsub[3], 10) != 0) {
            var ss = "mask_format_err";
            return ss;
        }
        return true;
    },
    check_mac(str) {//检查mac是否合法
        var err_obj = new Object;
        err_obj.mac_addr_err = "mac_err";
        if (str == "") {
            var ss = err_obj.mac_addr_err;
            return ss;
        }
        if (str == "00:00:00:00:00:00") {
            var ss = "mac_not_0";
            return ss;
        }
        var tmp_str = str.toUpperCase();
        if (tmp_str == "FF:FF:FF:FF:FF:FF") {
            var ss = "mac_not_f";
            return ss;
        }
        if (str.length != 17) {
            var ss = err_obj.mac_addr_err;
            return ss;
        }
        var pattern = "/^([0-9A-Fa-f]{2})(:[0-9A-Fa-f]{2}){5}/";
        eval("var pattern=" + pattern);
        var ck = pattern.test(str);
        if (ck == false) {
            var ss = err_obj.mac_addr_err;
            return ss;
        }
        if (str.substring(0, 2) == '01') {
            var ss = "mac_broadcast_addr";
            return ss;
        }
        return true;
    },
    check_mtu(str) {
        var ret = checkObj.check_int(str);
        if (true != ret)
            return ret;
        if (parseInt(str, 10) > 1500 || parseInt(str, 10) < 1400) {
            var ss = "mtu_1500_1400";
            return ss;
        }
        return true;
    },
    check_pppoe_out_time(str) {
        var ss = checkObj.check_int(str);
        if (ss != true)
            return ss;
        if (str < 1 || str > 30) {
            ss = "pppoe_out_time_range";
            return ss;
        }
        return true;
    },
    check_year(str) {
        if (str == "" || str == null) {
            var ss = "non_null_integer";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_numeric_char";
                return ss;
            }
        }
        // if (parseInt(str, 10) < 2008) {
        //     var ss = _("year_lt_2008");
        //     return ss;
        // }
        return true;
    },
    check_month(str) {
        if (str == "" || str == null) {
            var ss = "non_null_integer";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_numeric_char";
                return ss;
            }
        }
        if (parseInt(str, 10) <= 0) {
            var ss = "month_range";
            return ss;
        }
        else if (parseInt(str, 10) > 12) {
            var ss = "month_range";
            return ss;
        }
    
        return true;
    },
    check_day(str) {
        var solarMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if (str == "" || str == null) {
            var ss = "non_null_integer";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_numeric_char";
                return ss;
            }
        }
        var y = get_ctrl_year();
        var month = get_ctrl_month();
        var day = "";
        if (month == 2) {
            day = (((y % 4 == 0) && (y % 100 != 0) || (y % 400 == 0)) ? 29 : 28);
        }
        else {
            month = month - 1;
            day = solarMonth[month];
        }
        // if (parseInt(str, 10) <= 0) {
        //     var ss = _("day_range + day");
        //     return ss;
        // }
        // else if (parseInt(str, 10) > day) {
        //     var ss = _("day_range") + day;
        //     return ss;
        // }
        return true;
    },
    check_hour(str) {
        if (str == "" || str == null) {
            var ss = "non_null_integer";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_numeric_char";
                return ss;
            }
        }
        if (parseInt(str, 10) < 0) {
            var ss = "hour_range";
            return ss;
        }
        else if (parseInt(str, 10) > 23) {
            var ss = "hour_range";
            return ss;
        }
        return true;
    },
    check_min(str) {
        if (str == "" || str == null) {
            var ss = "non_null_integer";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_numeric_char";
                return ss;
            }
        }
        if (parseInt(str, 10) < 0) {
            var ss = "minute_range";
            return ss;
        }
        else if (parseInt(str, 10) > 59) {
            var ss = "minute_range";
            return ss;
        }
        return true;
    },
    check_sec(str) {
        if (str == "" || str == null) {
            var ss = "non_null_integer";
            return ss;
        }
        var cmp = '0123456789';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (cmp.indexOf(tst) < 0) {
                var ss = "non_numeric_char";
                return ss;
            }
        }
        if (parseInt(str, 10) < 0) {
            var ss = "second_range";
            return ss;
        }
        else if (parseInt(str, 10) > 59) {
            var ss = "second_range";
            return ss;
        }
        return true;
    },
    
    check_calendar(str) {//检验日期格式为YYYY-MM-DD
        if (str == "" || str == null) {
            var ss = "calendar_not_null";
            return ss;
        }
        var parts;
        var msg = "calendar_format_err";
        if (str.indexOf("-") > -1) {
            parts = str.split('-');
        } else {
            return msg;
        }
        if (parts.length < 3) {
            return msg;
        }
        for (i = 0; i < 3; i++) {
            if (isNaN(parseInt(parts[i], 10))) {
                return msg;
            }
        }
        var y = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10);
        var d = parseInt(parts[2], 10);
        if (y < 1900 || y > 3000) {
            var ss = "year_err";
            return ss;
        }
        if (m < 1 || m > 12) {
            var ss = "month_err";
            return ss;
        }
        var ss_msg = "day_err";
        if (d < 1 || d > 31) {
            return ss_msg;
        }
        switch (d) {
            case 29:
                if (m == 2) {
                    if ((y % 4 == 0) && (y % 100 != 0)) {
                        return true;
                    }
                    else if (y % 400 == 0) {
                        return true;
                    }
                    else
                        return ss_msg;
                }
                break;
            case 30:
                if (m == 2)
                    return ss_msg;
                break;
            case 31:
                if (m == 2 || m == 4 || m == 6 || m == 9 || m == 11)
                    return ss_msg;
                break;
            default:
                break;
        }
        return true;
    },
    check_url(str) {
        if (str == "" || str == null) {
            var ss = "url_not_null";
            return ss;
        }
        var cmp = '<>(),;+[]{} ';
        var buf = str;
        for (var h = 0; h < buf.length; h++) {
            var tst = buf.substring(h, h + 1);
            if (tst == ".") {
                var temp = buf.substring(h + 1, h + 2);
                if (temp == "." || h == buf.length - 1) {
                    var ss = "url_err";
                    return ss;
                }
            }
            if (cmp.indexOf(tst) >= 0) {
                var ss = "not_illegal_char";
                return ss;
            }
            if (tst.charCodeAt(0) < 0 || tst.charCodeAt(0) > 255) {
                var ss = "not_chinese";
                return ss;
            }
        }
        return true;
    },
    check_eq5(str) {
        var ss = "eq_5";
        if (str.length != 5)
            return ss;
        return true;
    },
    check_eq4_20(str) {
        var ss = "eq4_20";
        if (str.length < 4 || str.length > 20)
            return ss;
        return true;
    },
    check_eq6_20(str) {
        var ss = "eq6_20";
        if (str.length < 6 || str.length > 20)
            return ss;
        return true;
    },
    check_eq8_63(str) {
        var ss = "eq8_63";
        if (str.length < 8 || str.length > 63)
            return ss;
        return true;
    },
    check_eq8_64(str) {
        var ss = "eq8_64";
        if (str.length < 8 || str.length > 64){
            return ss;
        }
        if(str.length == 64 && !checkObj.check_isHEXValid(str)){
            var ss = "eq_he";
            return ss;
        }
        return true;
    },
    check_isHEXValid(){
        for ( i=0; i<SN.length; i++ )
        {
            var ch=SN.charAt( i );
            if ((( ch<'0') || (ch>'9' )) && 
                ((ch >'f' )||(ch <'a'))  && 
                ((ch >'F' )||(ch <'A')))
            {
                return false;
            }

        }
        return true;
    },
    check_frag(str) {
        var ret = checkObj.check_int(str);
        if (true != ret)
            return ret;
        if (parseInt(str, 10) > 2346 || parseInt(str, 10) < 256) {
            var ss = "frag";
            return ss;
        }
        return true;
    },
    check_rts(str) {
        var ret = checkObj.check_int(str);
        if (true != ret)
            return ret;
        if (parseInt(str, 10) > 2347 || parseInt(str, 10) < 0) {
            var ss = "rts";
            return ss;
        }
        return true;
    },
    check_acktime(str) {
        var ret = checkObj.check_int(str);
        if (true != ret)
            return ret;
        if (parseInt(str, 10) > 255 || parseInt(str, 10) < 0) {
            var ss = "acktime";
            return ss;
        }
        return true;
    },
    check_beacon(str) {
        var ret = checkObj.check_int(str);
        if (true != ret)
            return ret;
        if (parseInt(str, 10) > 1024 || parseInt(str, 10) < 100) {
            var ss = "beacon";
            return ss;
        }
        return true;
    },
    check_maxuser(str) {
        var ret = checkObj.check_int(str);
        var tos=str.toString();
        var ll=tos.length;
        var sfirst=tos[0];
        //console.log(ll);
        if (true != ret)
            return ret;
        if (parseInt(str, 10) > 64 || parseInt(str, 10) < 1) {
            var ss = "maxuser";
            return ss;
        }else if((ll>=2)&&(sfirst=="0")){
            var ss= "maxuser";
            return ss;
        }
        return true;
    },
    check_apthreshold(str){
        if((str == "") || (str.length > 3)
            || isNaN(parseInt_dec(str))
            || (parseInt_dec(str) < -95) 
            ||(parseInt_dec(str) >-65))
        {
            var ss = "apthreshold";
            return ss;
        }else {
            var cmp = '0123456789';
            var l = str.length;
            var buf = str.slice(1, l);
            for (var h = 0; h < buf.length; h++) {
                var tst = buf.substring(h, h + 1);
                if (cmp.indexOf(tst) < 0) {
                    var ss = "non_numeric_char";
                    return ss;
                }
            }
        }
        return true;
    },
    check_adress(str) {
        if (str == "" || str == null) {        
            return "address_no_empty";
        }
        return true;
    },
}

export default checkObj;


