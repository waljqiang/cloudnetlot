<template >
<div>
    <el-tabs 
    id="conf_tabs"
    v-model="activeName" 
    @tab-click="handleClick" 
    :stretch="isstretch"
    :destroy-on-close="true">
        
        <el-tab-pane v-if="is_master!='1'&&types=='batch_wifi'" label="无线批量设置" name="batch_wifi">
            <wireless-batch-counter v-if="types=='batch_wifi'" :devmac="devmac"></wireless-batch-counter>
        </el-tab-pane>
        <el-tab-pane v-if="types=='conf'" label="设备信息" name="first">
            <div v-if="infoForm!=''">
                <p class="info_title">
                    <span>基本信息</span>
                    <span class="el-dropdown frt mr20" style="text-indent:0" @click="refInfo" > 
                        <i class="el-icon-refresh"></i>
                    </span>
                </p>
                <div class="clear"></div>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >设备型号</el-col>
                    <el-col :span="14">{{infoForm.system.type}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >设备名称</el-col>
                    <el-col :span="14">{{infoForm.system.name}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >设备MAC</el-col>
                    <el-col :span="14">{{infoForm.system.mac}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >设备IP</el-col>
                    <el-col :span="14">{{infoForm.system.dev_ip}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >运行时间</el-col>
                    <el-col :span="14">{{disp_time(infoForm.system.runtime)}}</el-col>
                </el-row>

                <p class="info_title">无线信息</p>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >SSID</el-col>
                    <el-col :span="14">{{infoForm.wifi.ssidVal}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >BSSID</el-col>
                    <el-col :span="14">{{infoForm.wifi.bssidVal}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >信道</el-col>
                    <el-col :span="14">{{infoForm.wifi.channelVal}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >无线安全</el-col>
                    <el-col :span="14">{{infoForm.wifi.encVal}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >发射功率</el-col>
                    <el-col :span="14">{{infoForm.wifi.powerVal}}</el-col>
                </el-row>

                <p class="info_title">其它信息</p>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >信标帧时间间隔</el-col>
                    <el-col :span="14">{{infoForm.wifi.beaconVal}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >AP覆盖阈值</el-col>
                    <el-col :span="14">{{infoForm.wifi.coverVal}}</el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >定时重启</el-col>
                    <el-col :span="14">
                        <template v-if="infoForm.time_reboot.enable=='0'">
                            关闭
                        </template>
                        <template v-else>
                            开启
                            <!-- <template v-if="infoForm.time_reboot.time.indexOf('day')>=0">

                            </template> -->
                        </template>
                    </el-col>
                </el-row>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >固件版本</el-col>
                    <el-col :span="14">{{infoForm.system.version}}</el-col>
                </el-row>
                
                <p class="info_title">用户信息</p>
                <el-row class="item_info">
                    <el-col :offset="2" :span="6" >用户数</el-col>
                    <el-col :span="14">
                        <el-button type="primary" size="mini" @click="dialogShow" >查看用户列表</el-button>
                    </el-col>
                </el-row>
            </div>
        </el-tab-pane>
        <el-tab-pane  v-if="is_master!='1'&&types=='conf'" label="无线设置" name="second">
            <wireless-counter v-if="infoForm" :w_data="infoForm.wifi" :devmac="devmac"></wireless-counter>
        </el-tab-pane>
        <el-tab-pane  v-if="is_master!='1'&&types=='conf'" label="设备重启" name="third">
            <reboot-counter v-if="infoForm" :data="infoForm.time_reboot" :devmac="devmac" @back="getParentDevList" ></reboot-counter>
        </el-tab-pane>
        <el-tab-pane  v-if="is_master!='1'&&(types=='batch_warn'||types=='conf')" el-tab-pane :label="warnTitle" name="fourth">
            <warn-counter></warn-counter>
        </el-tab-pane>
    </el-tabs> 
    <el-dialog
        v-if="infoForm!=''"
        :close-on-click-modal="false"
        :visible.sync="dialogVisible"
        width="600px"
        append-to-body
        :show-close="false">
            <div slot="title" class="frt" >
                <div class="custom_close align_c" @click="dialogClose" >
                    <i class="el-icon-close" />
                </div>
            </div>
            <div>
                <el-row class="item_info align_c light_gray_bg" style="line-height:35px">
                    <el-col :span="2" >序号</el-col>
                    <el-col :span="7">设备名称</el-col>
                    <el-col :span="7">设备IP</el-col>
                    <el-col :span="8">设备MAC</el-col>
                </el-row>
                <el-scrollbar style="height:200px" id="main_scrol">
                    <template v-for="(item,index) in infoForm.user.list">
                        <el-row class="item_info align_c"  :key="index" >
                            <el-col :span="2" >{{index+1}}</el-col>
                            <el-col :span="7"><div class="txt_el" v-html="item.name?item.name:'&nbsp;&nbsp;'"></div></el-col>
                            <el-col :span="7">{{item.ip}}</el-col>
                            <el-col :span="8">{{item.mac}}</el-col>
                        </el-row>
                    </template>
                </el-scrollbar>
            </div>
    </el-dialog>
</div>
</template>
<script>
import {devInfos,refresh} from '@/projects/home/api/device'
import wireless_from from './wireless'
import wireless_batch_from from './wireless_batch'
import reboot_from from './reboot'
import warn_from from './warn_thre'
import store from '@/projects/home/store'
export default {
    props:['types','devmac','actions'],
    data() {
        return {
            is_master:store.state.user.is_primary,
            activeName: '',
            isstretch:true,
            infoForm:'',
            dialogVisible:false,
            warnTitle:'',
            aaa:'&nbsp;',
            encObj : ['',this.$t('device.enc_open'),'WEP','WPAPSK','WPA2PSK','WPAWPA2PSK','WPA3PSK','WPA2WPA3PSK','WPAWPA2EAP','WPA2WPA3EAP']
        };
    },
    components:{
        'wireless-counter':wireless_from,
        'reboot-counter':reboot_from,
        'warn-counter':warn_from,
        'wireless-batch-counter':wireless_batch_from
    },
    methods: {
        handleClick(tab, event) {
            // console.log(tab, event);
        },
        getInfo(){
            let getData = {
                    "mac":this.devmac,
                    "type":[2,3,4,5,6]
                }
            devInfos(getData).then(response => { 
            if(response.status==10000){
                this.$store.commit('showloadding',{show:false}); 
                    response.data.wifi['channelVal'] ='';
                    response.data.wifi['powerVal'] = '';
                    response.data.wifi['beaconVal'] = '';
                    response.data.wifi['coverVal'] = '';
                    
                    response.data.wifi['ssidVal'] = '';
                    response.data.wifi['bssidVal'] = '';
                    response.data.wifi['encVal'] = '';
                    for(var i=0;i<response.data.wifi.list.length;i++){
                        let wifiInfo = response.data.wifi.list[i]
                        response.data.wifi['channelVal'] += wifiInfo.channel + '/';
                        response.data.wifi['powerVal'] += wifiInfo.power+"%" + '/';
                        response.data.wifi['beaconVal'] += wifiInfo.beacon_interval + '/';
                        response.data.wifi['coverVal'] += wifiInfo.coveragethreshold + '/';
                        
                        response.data.wifi['ssidVal'] += wifiInfo.vap[0].ssid + '/';
                        response.data.wifi['bssidVal'] += wifiInfo.vap[0].bssid + '/';
                        response.data.wifi['encVal'] += this.encObj[wifiInfo.vap[0].encode] + '/';
                    }
                    response.data.wifi.channelVal = this.subVal(response.data.wifi.channelVal);
                    response.data.wifi.powerVal = this.subVal(response.data.wifi.powerVal);
                    response.data.wifi.beaconVal = this.subVal(response.data.wifi.beaconVal);
                    response.data.wifi.coverVal = this.subVal(response.data.wifi.coverVal);
                    response.data.wifi.ssidVal = this.subVal(response.data.wifi.ssidVal);
                    response.data.wifi.bssidVal = this.subVal(response.data.wifi.bssidVal);
                    response.data.wifi.encVal = this.subVal(response.data.wifi.encVal);
                    this.infoForm = response.data;
            }
            }).catch((error) => {
                this.$message({
                    message: '',
                    type: 'error',
                    offset:100
                });
            })
        },
        dialogShow(){
            this.dialogVisible = true;
        },
        dialogClose(){
            this.dialogVisible = false;
        },
        subVal(data){
            return data.substr(0,data.length-1);
        },
        disp_time(sysuptime){
            if(sysuptime=='-1'){
                return "N/A"
            }
            var conn_time=parseInt(sysuptime);
            var day = (conn_time-conn_time%86400)/86400;
            conn_time = (conn_time-day*86400); 
            var hr = (conn_time-conn_time%3600)/3600;
            conn_time = (conn_time-hr*3600);
            var min =(conn_time-conn_time%60)/60;
            var sec = (conn_time - min*60) ;
            var uptime; //= day+_("day")+hr+_("hour")+min+_("minute")+sec+_("second");
            if(hr<=9){
                hr = "0"+hr;
            }
            if(min<=9){
                min = "0"+min;
            }
            if(sec<=9){
                sec = "0"+sec;
            }
            if(day!=0){
                uptime = day+this.$t("device.dev_day")+hr+":"+min+":"+sec;
            }else{
                uptime = hr+":"+min+":"+sec
            }
            return uptime;	
        },
        refInfo(){
            this.$store.commit('showloadding',{show:true}); 
            let getData = {
                macs:[this.devmac],
                type:[2,3,4,5,6]
            }
            refresh(getData).then(response => {  
               
                if(response.status==10000){
                    this.getInfo();
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
        getParentDevList(){
            this.$emit('list');
            this.$emit('count');
        }
    },
    created(){
        
        if(this.types=='conf'){
            this.$store.commit('showloadding',{show:true}); 
            this.getInfo();
        }
    },
    beforeMount(){
        if(this.types=='conf'){
            this.activeName = 'first';
            this.warnTitle='告警设置';
            this.isstretch = true;
        }else{
            this.isstretch = false;
            if(this.types=='batch_wifi'){
                this.activeName = 'batch_wifi';
            }else if(this.types=='batch_warn'){
                this.activeName = 'fourth';
                this.warnTitle='告警批量设置';
            }
            
        }
    },
    mounted(){
       if(this.types!='conf'){
           document.getElementsByClassName('el-tabs__nav-wrap')[0].style.padding = '0 0 0 35px';
       }
       console.log(this.types)
    }
}
</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.info_title {text-indent: 30px; font-weight: bold;}
.item_info { line-height: 28px;}
.el-dropdown {
    cursor: pointer;    
    padding: 5px;
    background-color: $light_gray_bg;
    border: 1px solid $light_gray_border2; 
    border-radius:4px;
}
.el-dropdown:hover{
    color: #409EFF; 
}
</style>