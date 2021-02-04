<template >
     <el-scrollbar style="height:calc(100vh - 110px)" id="main_scrol">
         <el-form v-if="w_from!=''" label-position="left" label-width="32%" size="mini" :model="w_from" style="width:85%; margin:0 auto">
            <el-form-item label="无线选择">
                <el-select v-model="w_from.index">
                    <el-option
                        v-for="(item,index) in w_from.list" 
                        :key="index" 
                        :label="'无线设备'+(index+1)" 
                        :value="index"
                    >
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="无线状态">
                <el-radio v-model="w_from.list[w_from.index].vap[0].enable" label="1">启用</el-radio>
                <el-radio v-model="w_from.list[w_from.index].vap[0].enable" label="0">禁用</el-radio>
            </el-form-item>
            <el-form-item label="WiFi名称">
                <el-input v-model="w_from.list[w_from.index].vap[0].ssid" maxlength="32" ></el-input>
            </el-form-item>
            <el-form-item label="隐藏WiFi">
                <el-radio v-model="w_from.list[w_from.index].vap[0].ssid_hide" label="1">启用</el-radio>
                <el-radio v-model="w_from.list[w_from.index].vap[0].ssid_hide" label="0">禁用</el-radio>
            </el-form-item>
            <!-- <el-form-item label="无线带宽">
                <el-select v-model="w_from.list[w_from.index].bandwidth">
                    <el-option
                        v-for="(bd,index) in options.bandwidth" 
                        :key="index" 
                        :label="bd" 
                        :value="bd"
                    >
                    </el-option>
                </el-select>
            </el-form-item> -->
            <el-form-item label="信道">
                <el-select v-model="w_from.list[w_from.index].channel">
                    <el-option
                        v-for="(channel,index) in options.channel" 
                        :key="index" 
                        :label="channel" 
                        :value="channel"
                    >
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="加密方式">
                <el-select v-model="w_from.list[w_from.index].vap[0].encode">
                    <el-option
                        v-for="(item,index) in options.encode" 
                        :key="index" 
                        :label="encObj[item]" 
                        :value="item.toString()"
                    >
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="密码" v-show="w_from.list[w_from.index].vap[0].encode!='1'" >
                <el-input show-password v-model="w_from.list[w_from.index].vap[0].password"  maxlength="64" ></el-input>
            </el-form-item>
            <el-form-item label="功率">
                <el-select v-model="w_from.list[w_from.index].power">
                    <el-option
                        v-for="(item,index) in options.power" 
                        :key="index" 
                        :label="item+'%'" 
                        :value="item"
                    >
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="VLAN ID">
                <el-input v-model="w_from.list[w_from.index].vap[0].vlan_id" maxlength="4" >
                    <span class="align_r" slot="suffix">
                        3-4094，0表示关闭
                    </span>
                </el-input>
            </el-form-item>
            <p class="blue_font mt20 mb20 cur">
                <span @click="showAdv = !showAdv">
                    高级设置
                    <i style="padding:0 5px" :class="showAdv?'el-icon-minus':'el-icon-plus'"></i>
                </span>
            </p>
            <el-collapse-transition>
                <div v-show="showAdv">
                    <el-form-item label="无线定时关闭">
                        <el-radio v-model="w_from.list[w_from.index].timer.enable" label="1">启用</el-radio>
                        <el-radio v-model="w_from.list[w_from.index].timer.enable" label="0">禁用</el-radio>
                    </el-form-item>
                    <el-form-item label="时间范围" v-show="w_from.list[w_from.index].timer.enable=='1'" >
                        <el-row>
                            <el-col :span="11" >
                                <el-time-picker
                                    style="width:auto"
                                    :editable="false"
                                    :clearable="false"
                                    format="HH:mm"
                                    value-format="HH:mm"
                                    v-model="w_from.list[w_from.index].timer.start"
                                    placeholder="起始时间">
                                </el-time-picker>
                            </el-col>
                            <el-col :span="2" class="align_c" >--</el-col>
                            <el-col :span="11" >
                                <el-time-picker
                                    style="width:auto"
                                    :editable="false"
                                    :clearable="false"
                                    format="HH:mm"
                                    value-format="HH:mm"
                                    v-model="w_from.list[w_from.index].timer.end"
                                    placeholder="起始时间">
                                </el-time-picker>
                            </el-col>
                        </el-row>
                    </el-form-item>
                    <el-form-item label="无线模式">
                        <el-select v-model="w_from.list[w_from.index].phymode">
                            <el-option
                                v-for="(item,index) in options.phymode" 
                                :key="index" 
                                :label="phyObj[item]" 
                                :value="index.toString()"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="用户隔离">
                        <el-radio v-model="w_from.list[w_from.index].user_isolate" label="1">启用</el-radio>
                        <el-radio v-model="w_from.list[w_from.index].user_isolate" label="0">禁用</el-radio>
                    </el-form-item>
                    <el-form-item label="分片阈值">
                        <el-input v-model="w_from.list[w_from.index].frag_threshold" maxlength="4" >
                            <span class="align_r" slot="suffix">
                                (256~2346)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="RTS阈值">
                        <el-input v-model="w_from.list[w_from.index].rts_threshold" maxlength="4">
                            <span class="align_r" slot="suffix">
                                (50~2347)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="信标帧时间间隔">
                        <el-input v-model="w_from.list[w_from.index].beacon_interval" maxlength="4">
                            <span class="align_r" slot="suffix">
                                (50ms~1024ms)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="Short GI">
                        <el-radio v-model="w_from.list[w_from.index].shortgi" label="1">启用</el-radio>
                        <el-radio v-model="w_from.list[w_from.index].shortgi" label="0">禁用</el-radio>
                    </el-form-item>
                    <el-form-item label="AP覆盖阈值">
                        <el-input v-model="w_from.list[w_from.index].coveragethreshold" maxlength="4">
                            <span class="align_r" slot="suffix">
                                (-95dBm ~ -65dBm)
                            </span>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="最大接入用户数">
                        <el-input v-model="w_from.list[w_from.index].max_sta" maxlength="4">
                            <span class="align_r" slot="suffix">
                                1~64，0表示没有限制
                            </span>
                        </el-input>
                    </el-form-item>
                </div>
            </el-collapse-transition>
            <el-button size="mini" type="primary" class="frt" @click="saveData"  >{{$t("common.apply_config")}}</el-button>
        </el-form>
        
     </el-scrollbar>
</template>
<script>
import {wifiInfos,wifiOptions,setWifi} from '@/projects/home/api/device'
//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';
export default {
    props:['w_data','devmac'],
    data() {
        return {
            w_from:'',
            options:'',
            showAdv:false,
            encObj : ['',this.$t('device.enc_open'),'WEP','WPAPSK','WPA2PSK','WPAWPA2PSK','WPA3PSK','WPA2WPA3PSK','WPAWPA2EAP','WPA2WPA3EAP'],
            phyObj : ['自适应','11A','11B','11G','FH','TURBO_A','TURBO_G',
                    '11NA_HT20','11NG_HT20','11NA_HT40PLUS','11NA_HT40MINUS',
                    '11NG_HT40PLUS','11NG_HT40MINUS','11NG_HT40','11NA_HT40','11AC_VHT20',
                    '11AC_VHT40PLUS','11AC_VHT40MINUS','11AC_VHT40','11AC_VHT80','11AC_VHT160'
            ]      
        }
    },
    methods: {
        getOptions(){
            let getData = {
                mac:this.devmac,
                radio:this.w_from.index
            }
            wifiOptions(getData).then(response => {  
                if(response.status==10000){
                    this.options = response.data;
                    console.log(this.options)
                }
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                this.$message({
                    message: '',
                    type: 'error',
                    offset:100
                });
            })
        },
        getInfo(){
            let getData = {
                mac:this.devmac,
                radio:this.w_from.index
            }
            wifiInfos(getData).then(response => {  
                if(response.status==10000){
                    this.options = response.data;
                }
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                this.$message({
                    message: '',
                    type: 'error',
                    offset:100
                });
            })
        },
        saveData(){
            if(this.checkData()!=true){
                return false;
            }
            this.$store.commit('showloadding',{show:true}); 
            let getData = {
                mac:this.devmac,
                radio:this.w_from.index,
                "vaps":["0"],
                "options":{
                    "ssid":this.w_from.list[this.w_from.index].vap[0].ssid,
                    "enable":this.w_from.list[this.w_from.index].vap[0].enable,
                    "ssid_hide":this.w_from.list[this.w_from.index].vap[0].ssid_hide,
                    // "bandwidth":this.w_from.list[this.w_from.index].bandwidth,
                    "channel":this.w_from.list[this.w_from.index].channel,
                    "encode":this.w_from.list[this.w_from.index].vap[0].encode,
                    "password":this.w_from.list[this.w_from.index].vap[0].password,
                    "power":this.w_from.list[this.w_from.index].power,
                    "vlan_id":this.w_from.list[this.w_from.index].vap[0].vlan_id,
                    "timer_enable":this.w_from.list[this.w_from.index].timer.enable,
                    "timer_start":this.w_from.list[this.w_from.index].timer.start,
                    "timer_end":this.w_from.list[this.w_from.index].timer.end,
                    "phymode":this.w_from.list[this.w_from.index].phymode,
                    "user_isolate":this.w_from.list[this.w_from.index].user_isolate,
                    "frag_threshold":this.w_from.list[this.w_from.index].frag_threshold,
                    "rts_threshold":this.w_from.list[this.w_from.index].rts_threshold,
                    "beacon_interval":this.w_from.list[this.w_from.index].beacon_interval,
                    "shortgi":this.w_from.list[this.w_from.index].shortgi,
                    "coveragethreshold":this.w_from.list[this.w_from.index].coveragethreshold,
                    "max_sta":this.w_from.list[this.w_from.index].max_sta
                }
            }
            setWifi(getData).then(response => {
                this.$store.commit('showloadding',{show:false});   
                if(response.status==10000){
                    this.$message.success(this.$t('msg.set_success_tips'));  
                }
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400100:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400109:
                        err.message = this.$t('msg.is_no_per_tips2');
                        break;
                    case 600400199:
                        err.message = this.$t('msg.ssid_not_empty');
                        break;
                    case 600400202:
                        err.message = this.$t('msg.pwd_format_error');
                        break;
                    case 600400209:
                        err.message = this.$t('msg.frag');
                        break;
                    case 600400210:
                        err.message = this.$t('msg.rts');
                        break;
                    case 600400211:
                        err.message = this.$t('msg.beacon');
                        break;
                    default:
                        err.message = error.message;
                }
                this.$message({
                    message: err.message,
                    type: 'error',
                    offset:100
                });
            })
        },
        checkData(){
            let rules = [
                {val: this.w_from.list[this.w_from.index].vap[0].ssid, rule: "check_ssid"},
                {val: this.w_from.list[this.w_from.index].vap[0].vlan_id, rule: "check_vlan"},
                {val: this.w_from.list[this.w_from.index].frag_threshold, rule: "check_frag"},
                {val: this.w_from.list[this.w_from.index].rts_threshold, rule: "check_rts"},
                {val: this.w_from.list[this.w_from.index].beacon_interval, rule: "check_beacon"},
                {val: this.w_from.list[this.w_from.index].coveragethreshold, rule: "check_apthreshold"},
                {val: this.w_from.list[this.w_from.index].max_sta, rule: "check_maxuser"}
            ]
            let encType = this.w_from.list[this.w_from.index].vap[0].encode;
            let pwd = this.w_from.list[this.w_from.index].vap[0].password;
            if(encType!='1'){
                if(encType=='2'){
                    let Obj = {val: pwd, rule: "wepkey"}
                    rules.splice(1,0,Obj)
                }else{
                    let Obj = {val: pwd, rule: "check_password check_eq8_64"}
                    rules.splice(1,0,Obj)
                }
            }
            for(var i=0;i<rules.length;i++){
                var types = rules[i].rule.split(' ');
                for (var p in types) {
                    if (types[p] == "noneed")
                        continue;
                    var reg_type = types[p];
                    let val = rules[i].val;
                    var res = checkObj[reg_type](val);
                    if (res != true) { 
                        this.$message({
                            message: this.$t("check."+res),
                            type: 'error',
                            offset:100
                        });         
                        return false;
                    }
                }
            }
            let tiemd_status = this.w_from.list[this.w_from.index].timer.enable;
            let start_time = this.w_from.list[this.w_from.index].timer.start;
            let end_time =  this.w_from.list[this.w_from.index].timer.end;
            if(tiemd_status=='1'&&(start_time == end_time)){
                this.$message({
                    message: this.$t("check.timed_start_end_tips"),
                    type: 'error',
                    offset:100
                });
                return false;
            }
            return true;
        }
    },
    created(){
        this.w_from = this.w_data;
        this.w_from.index = 0;
        this.getOptions();
    },
    mounted(){
       
        console.log(this.w_data)
    }
}
</script>
<style scoped>

</style>