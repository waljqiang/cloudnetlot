<template >
    <div class="form_box">
        <el-tabs v-model="activeName" style="width:600px" >
            <el-tab-pane label="无线配置" name="w_basic_set">
                <el-form label-width="40%" >
                    <el-form-item label="无线选择" >
                        <el-select v-model="basic_form.w_index" >
                            <el-option v-for="(apNum,index) in basic_form.ap_data" :label="'无线设备'+(index+1)" :key="index" :value="index.toString()">

                            </el-option>
                        </el-select>
                    </el-form-item> 
                    <el-form-item label="无线状态">
                        <el-radio-group v-model="basic_form.ap_data[basic_form.w_index].w_status">
                            <el-radio label="1">开启</el-radio>
                            <el-radio label="0">关闭</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-form>
                <el-form label-width="40%" :disabled="basic_form.ap_data[basic_form.w_index].w_status=='0'" >
                    <el-form-item label="WiFi名称" >
                        <el-row>
                            <el-col :span="13">
                                <el-input v-model="basic_form.ap_data[basic_form.w_index].ssid" ></el-input>
                            </el-col>
                            <el-col :span="10" :offset="1">
                                <el-checkbox v-model="basic_form.ap_data[basic_form.w_index].hide_ssid" false-label="0" true-label="1" >隐藏WiFi</el-checkbox>
                            </el-col>
                        </el-row>  
                    </el-form-item>
                    <el-form-item label="加密方式" >
                        <el-select v-model="basic_form.ap_data[basic_form.w_index].enc_type" >
                            <el-option v-for="(item,index) in enc_type_list" :label="item.text" :key="index" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="密码">
                        <el-input v-model="basic_form.ap_data[basic_form.w_index].pwd" show-password ></el-input>
                    </el-form-item>
                    <el-form-item label="VlanID">
                        <el-input v-model="basic_form.ap_data[basic_form.w_index].v_id" >
                            <span style="text_align:right" slot="suffix">
                                (0~4094)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="启用虚拟AP">
                        <el-checkbox v-for="(item,index) in basic_form.ap_data[basic_form.w_index].vap" :key="index" v-model="item.w_status" false-label="0" true-label="1" @change="handleChange(item.w_status,index)" >{{"虚拟AP"+(index+1)}}</el-checkbox>
                    </el-form-item>
                </el-form>

                <el-tabs v-model="vapName" type="card" v-if="basic_form.ap_data[basic_form.w_index].w_status=='1'" >
                    <template v-for="(item,index) in basic_form.ap_data[basic_form.w_index].vap">
                        <el-tab-pane  v-if="item.w_status=='1'" :label='"虚拟AP"+(index+1)' :key="index" :name="'vap'+(index+1)">
                            <el-form class="demo-form-inline" label-width="40%" >
                                <el-form-item label="WiFi名称">
                                    <el-row>
                                        <el-col :span="13">
                                            <el-input v-model="item.ssid" ></el-input>
                                        </el-col>
                                        <el-col :span="10" :offset="1">
                                            <el-checkbox v-model="item.hide_ssid" false-label="0" true-label="1" >隐藏WiFi</el-checkbox>
                                        </el-col>
                                    </el-row>  
                                </el-form-item>
                                <el-form-item label="加密方式" >
                                    <el-select v-model="item.enc_type" >
                                        <el-option v-for="(item,index) in enc_type_list" :label="item.text" :key="index" :value="item.value"></el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="密码">
                                    <el-input v-model="item.pwd" ></el-input>
                                </el-form-item>
                                <el-form-item label="VlanID">
                                    <el-input v-model="item.v_id" >
                                        <span style="text_align:right" slot="suffix">
                                            (0~4094)
                                        </span>
                                    </el-input>
                                </el-form-item>
                            </el-form>
                        </el-tab-pane>
                    </template> 
                </el-tabs>
            </el-tab-pane>
            <el-tab-pane label="高级设置" name="w_adv_set">
                <el-form label-width="40%" >
                    <el-form-item label="无线选择" >
                        <el-select v-model="basic_form.w_index" >
                            <el-option v-for="(apNum,index) in basic_form.ap_data" :label="'无线设备'+(index+1)" :key="index" :value="index.toString()">

                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-form>
                <el-form label-width="40%" >
                    <el-form-item label="无线模式" >
                        <el-select v-model="basic_form.ap_data[basic_form.w_index].m_wirelessmode" >
                            <el-option v-for="(modeItem,index) in htmode_list['w'+basic_form.ap_data[basic_form.w_index].w_type]" :label="modeItem.text" :key="index" :value="modeItem.value">

                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="最大接入用户数">
                        <el-input v-model="basic_form.ap_data[basic_form.w_index].m_MaxStanum" ></el-input>
                    </el-form-item>
                    <el-form-item label="用户隔离">
                        <el-radio-group v-model="basic_form.ap_data[basic_form.w_index].m_wlan_isolate">
                            <el-radio label="1">开启</el-radio>
                            <el-radio label="0">关闭</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="Short GI">
                        <el-radio-group v-model="basic_form.ap_data[basic_form.w_index].m_wlan_shortGI">
                            <el-radio label="1">开启</el-radio>
                            <el-radio label="0">关闭</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="信标帧时间间隔">
                        <el-input v-model="basic_form.ap_data[basic_form.w_index].m_wlan_beacon_interval" maxlength="4" >
                            <span style="text_align:right" slot="suffix">
                                (0~4094)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="AP覆盖阈值">
                        <el-input v-model="basic_form.ap_data[basic_form.w_index].m_coverage_threshold" maxlength="4" >
                            <span style="text_align:right" slot="suffix">
                                (-95dBm~-65dBm)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="分包门限">
                        <el-input v-model="basic_form.ap_data[basic_form.w_index].m_wlan_frag" >
                            <span style="text_align:right" slot="suffix">
                                (256-2346)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="RTS门限">
                        <el-input v-model="basic_form.ap_data[basic_form.w_index].m_wlan_rts" >
                            <span style="text_align:right" slot="suffix">
                                (1-2347)
                            </span>
                        </el-input>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
            <el-tab-pane label="其他配置" name="w_other_set">
                <el-form label-width="40%" >
                    <el-form-item  label="定时重启">
                        <el-radio-group v-model="reboot_form.rebootEnable">
                            <el-radio label="1">开启</el-radio>
                            <el-radio label="0">关闭</el-radio>
                        </el-radio-group>
                    </el-form-item>

                    <el-form-item  label="重启类型" v-if="reboot_form.rebootEnable=='1'">
                        <el-radio-group v-model="reboot_form.rebootType">
                            <el-radio label="1">按时间重启</el-radio>
                            <el-radio label="2">按天重启</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item  label="重启时间"  v-if="reboot_form.rebootEnable=='1'" v-show="reboot_form.rebootType=='1'">
                        <el-select v-model="reboot_form.reboot_Hour" >
                            <el-option v-for="(rebootTime,index) in 24" :label="index+':00'" :key="index" :value="index.toString()">

                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item  label="重启间隔"  v-if="reboot_form.rebootEnable=='1'" v-show="reboot_form.rebootType=='2'">
                        <el-select v-model="reboot_form.reboot_Day" >
                            <el-option v-for="(rebootTime,index) in 10" :label="(index+1)+'天'" :key="index" :value="(index+1).toString()">

                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item  label="设备登录密码"  >
                        <el-input v-model="reboot_form.userpwd"  >
                            
                        </el-input>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>
        <el-button type="primary" @click="onSubmit" >提交</el-button>
    </div>
</template>
<script>
import w_data from './data.js';

export default {
    data() {
        return {
            activeName: 'w_basic_set',
            enc_type_list : [
                {"value":"1,9","text":"开放"},
                {"value":"9,7","text":"加密"}
            ],
            htmode_list : {
                w2 : [
                        {"value":"3","text":"11G"},
                        {"value":"8","text":"11NG_HT20"},
                        {"value":"13","text":"11NG_HT40"},
                    ],
                w5 : [
                        {"value":"1","text":"11A"},
                        {"value":"7","text":"11NA_HT20"},
                        {"value":"14","text":"11NA_HT40"},
                        {"value":"15","text":"11AC_VHT20"},
                        {"value":"18","text":"11AC_VHT40"},
                        {"value":"19","text":"11AC_VHT80"}
                    ]
            },
            basic_form:{
                w_index:"0",
                ap_data:[
                    {
                        w_type:"2",
                        w_status:"1",
                        ssid:'wireless 1',
                        hide_ssid:'1',
                        enc_type:'1',
                        pwd:'66666666',
                        v_id:'0',
                        m_MaxStanum: "64",
                        m_coverage_threshold: "-95",
                        m_radio_type: "13",
                        m_wirelessbwmode: "0",
                        m_wirelessmode: "8",
                        m_wlan_beacon_interval: "100",
                        m_wlan_frag: "2346",
                        m_wlan_isolate: "1",
                        m_wlan_rts: "2347",
                        m_wlan_shortGI: "0",
                        vap_index:'',
                        vap:[
                            {
                                w_type:"2",
                                w_status:"1",
                                ssid:'wireless 1_vap1',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'777777777',
                                v_id:'0'
                            },
                            {
                                w_type:"5",
                                w_status:"1",
                                ssid:'wireless 1_vap2',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'88888888',
                                v_id:'0'
                            },
                            {
                                w_type:"5",
                                w_status:"1",
                                ssid:'wireless 3_vap3',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'9999999999',
                                v_id:'0'
                            }
                        ]
                    },
                    {
                        w_type:"2",
                        w_status:"1",
                        ssid:'wireless 1',
                        hide_ssid:'1',
                        enc_type:'1',
                        pwd:'66666666',
                        v_id:'0',
                        m_MaxStanum: "64",
                        m_coverage_threshold: "-95",
                        m_radio_type: "13",
                        m_wirelessbwmode: "0",
                        m_wirelessmode: "8",
                        m_wlan_beacon_interval: "100",
                        m_wlan_frag: "2346",
                        m_wlan_isolate: "1",
                        m_wlan_rts: "2347",
                        m_wlan_shortGI: "0",
                        vap_index:'',
                        vap:[
                            {
                                w_type:"2",
                                w_status:"1",
                                ssid:'wireless 1_vap1',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'777777777',
                                v_id:'0'
                            },
                            {
                                w_type:"5",
                                w_status:"1",
                                ssid:'wireless 1_vap2',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'88888888',
                                v_id:'0'
                            },
                            {
                                w_type:"5",
                                w_status:"1",
                                ssid:'wireless 3_vap3',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'9999999999',
                                v_id:'0'
                            }
                        ]
                    },
                    {
                        w_type:"2",
                        w_status:"1",
                        ssid:'wireless 1',
                        hide_ssid:'1',
                        enc_type:'1',
                        pwd:'66666666',
                        v_id:'0',
                        m_MaxStanum: "64",
                        m_coverage_threshold: "-95",
                        m_radio_type: "13",
                        m_wirelessbwmode: "0",
                        m_wirelessmode: "8",
                        m_wlan_beacon_interval: "100",
                        m_wlan_frag: "2346",
                        m_wlan_isolate: "1",
                        m_wlan_rts: "2347",
                        m_wlan_shortGI: "0",
                        vap_index:'',
                        vap:[
                            {
                                w_type:"2",
                                w_status:"1",
                                ssid:'wireless 1_vap1',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'777777777',
                                v_id:'0'
                            },
                            {
                                w_type:"5",
                                w_status:"1",
                                ssid:'wireless 1_vap2',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'88888888',
                                v_id:'0'
                            },
                            {
                                w_type:"5",
                                w_status:"1",
                                ssid:'wireless 3_vap3',
                                hide_ssid:'1',
                                enc_type:'1',
                                pwd:'9999999999',
                                v_id:'0'
                            }
                        ]
                    },
                ]
            },
            reboot_form:{
                "rebootEnable": "0",
                "rebootType": "0",    //"2"
                "reboot_Day": "0",    //"5"
                "reboot_Hour": "0",
                "userpwd":"0"
            },
            vapName: 'vap1',
        };
    },
    methods: {
        handleChange(v1,index){
            if(v1=='0'&&('vap'+(index+1))==this.vapName){
                var vapItem = this.basic_form.ap_data[this.basic_form.w_index].vap;
                for(var i=0;i<vapItem.length;i++){
                    if(vapItem[i].w_status=='1'){
                        this.vapName = 'vap'+(i+1);
                        return;
                    }
                }
            }
            if(v1=='1'){
                this.vapName = 'vap'+(index+1)
            }
        },
        handleClick(tab, event) {
            console.log(tab, event);
        },
        onSubmit() {
            console.log('submit!');
            this.subWireless();
        },
        subWireless(){
            var subData = 'opcode=12&opcode_fun=10&m_index=0';
            var apArr = this.basic_form.ap_data;
            for(var i=0;i<this.basic_form.ap_data.length;i++){
                var postData = this.basic_form.ap_data[i];
                subData+='&m_MaxStanum'+i+'='+apArr[i].m_MaxStanum;
                subData+='&m_coverage_threshold'+i+'='+apArr[i].m_coverage_threshold;
                subData+='&m_wirelessmode'+i+'='+apArr[i].m_wirelessmode;
                subData+='&m_wlan_beacon_interval'+i+'='+apArr[i].m_wlan_beacon_interval;
                subData+='&m_wlan_isolate'+i+'='+apArr[i].m_wlan_isolate;
                subData+='&m_wlan_rts'+i+'='+apArr[i].m_wlan_rts;
                subData+='&m_wlan_shortGI'+i+'='+apArr[i].m_wlan_shortGI;
                for(var k=0;k<postData.vap.length;k++){
                    var vapSubObj = {};
                    if(k==0){
                        vapSubObj.w_status = apArr[i].w_status;
                        vapSubObj.ssid = apArr[i].ssid;
                        vapSubObj.hide_ssid = apArr[i].hide_ssid;
                        vapSubObj.enc_type = apArr[i].enc_type;
                        vapSubObj.pwd = apArr[i].pwd;
                        vapSubObj.v_id = apArr[i].v_id;
                    }else{
                        vapSubObj.w_status = apArr[i].vap[k].w_status;
                        vapSubObj.ssid = apArr[i].vap[k].ssid;
                        vapSubObj.hide_ssid = apArr[i].vap[k].hide_ssid;
                        vapSubObj.enc_type = apArr[i].vap[k].enc_type;
                        vapSubObj.pwd = apArr[i].vap[k].pwd;
                        vapSubObj.v_id = apArr[i].vap[k].v_id;
                    }
                    subData+='&m_wlan_enable'+i+k+'='+vapSubObj.m_wlan_shortGI;
                    subData+='&m_ssid'+i+k+'='+vapSubObj.ssid;
                    subData+='&m_wlan_hide_ssid'+i+k+'='+vapSubObj.hide_ssid;
                    subData+='&m_authmode'+i+k+'='+vapSubObj.enc_type.split(",")[0];
                    subData+='&m_securitycipher'+i+k+'='+vapSubObj.enc_type.split(",")[1];
                    subData+='&m_psk_keystr'+i+k+'='+vapSubObj.pwd;
                    subData+='&m_vlanid'+i+k+'='+vapSubObj.v_id;

                }
            }
            console.log(subData)
            this.subTimereboot();
        },
        subTimereboot(){
            var subData = 'opcode=12&opcode_fun=1&m_index=0&m_TimeRebootEnable='+this.reboot_form.rebootEnable+'&m_TimeRebootType='+this.reboot_form.rebootType;
                subData += '&m_TimeReboot_Hour='+this.reboot_form.reboot_Hour+'&m_TimeReboot_Day=0&m_TimeReboot_Month=0&m_TimeReboot_Week=0';
            console.log(subData)
            this.subUserpwd();
        },
        subUserpwd(){
            var subData = 'opcode=12&opcode_fun=2&m_index=0&m_userpwd='+this.reboot_form.userpwd;
            console.log(subData)
        }
    },
    mounted(){
        var apData = this.basic_form.ap_data;
        for(var i=0;i<w_data.m_radio.length;i++){
            var radio_data = w_data.m_radio[i];
            apData[i].m_MaxStanum = radio_data.m_MaxStanum;
            apData[i].m_coverage_threshold = radio_data.m_coverage_threshold;
            apData[i].m_radio_type = radio_data.m_radio_type;
            apData[i].m_wirelessbwmode = radio_data.m_wirelessbwmode;
            apData[i].m_wirelessmode = radio_data.m_wirelessmode;
            apData[i].m_wlan_beacon_interval = radio_data.m_wlan_beacon_interval;
            apData[i].m_wlan_frag = radio_data.m_wlan_frag;
            apData[i].m_wlan_isolate = radio_data.m_wlan_isolate;

            apData[i].m_wlan_rts = radio_data.m_wlan_rts;
            apData[i].m_wlan_shortGI = radio_data.m_wlan_shortGI;
            apData[i].vap_index = '1';
            if(radio_data.m_wirelessbwmode=='0'){
                apData[i].w_type = '2';
            }else{
                apData[i].w_type = '5';
            }
            for(var j=0;j<radio_data.m_vap.length;j++){
                var vap_data = radio_data.m_vap[j];
                if(j==0){
                    apData[i].w_status = vap_data.m_wlan_enable;
                    apData[i].ssid = vap_data.m_ssid;
                    apData[i].hide_ssid = vap_data.m_wlan_hide_ssid;
                    apData[i].enc_type = vap_data.m_authmode+","+vap_data.m_securitycipher;
                    apData[i].pwd = vap_data.m_wep_keystr;
                    apData[i].v_id = vap_data.m_vlanid;
                }else{
                    var apDataChildIndex = j-1;
                    apData[i].vap[apDataChildIndex].w_status = vap_data.m_wlan_enable;
                    apData[i].vap[apDataChildIndex].ssid = vap_data.m_ssid;
                    apData[i].vap[apDataChildIndex].hide_ssid = vap_data.m_wlan_hide_ssid;
                    apData[i].vap[apDataChildIndex].enc_type = vap_data.m_authmode+","+vap_data.m_securitycipher;
                    apData[i].vap[apDataChildIndex].pwd = vap_data.m_wep_keystr;
                    apData[i].vap[apDataChildIndex].v_id = vap_data.m_vlanid;
                }
            }
        }
        this.reboot_form.rebootEnable = w_data.m_radio[2].m_timereboot.m_TimeRebootEnable;
        this.reboot_form.rebootType = w_data.m_radio[2].m_timereboot.m_TimeRebootType;
        this.reboot_form.reboot_Day = w_data.m_radio[2].m_timereboot.m_TimeReboot_Day;
        this.reboot_form.reboot_Hour = w_data.m_radio[2].m_timereboot.m_TimeReboot_Hour;
        this.reboot_form.userpwd = w_data.m_radio[2].m_weblogin.m_userpwd;
    }
}
</script>
<style scoped>
.el-form-item__label { text-align: left;}
.el-select {display: block;}
</style>