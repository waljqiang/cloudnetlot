<template>
    <div>
        <div class="" style="height:38px">
            <el-dropdown :hide-on-click="false" class="frt" @command="haClick" >
                <span class="">
                     <img src="@/public/img/home/batch_ico.png" class="va" width="15" height="15" >
                     <i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item  class="blue_bg white_font white_border dropdown_li" command="reset_pwd">{{$t('common.reset_pwd')}}</el-dropdown-item>
                    <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="del_account">{{$t('common.del_account')}}</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
            <div class="el-dropdown-link frt mr20" style="height:28px;">
                    <img src="@/public/img/home/add_user.png" class="va" width="15" height="15" >
            </div>
            <div class=" frt mr20">
                <el-input placeholder="请输入内容" size="mini" class="blue_font" v-model="search" >
                    <el-button slot="append" >
                        <img src="@/public/img/home/search.png" class="va" width="15" height="15" >
                    </el-button>
                </el-input>
            </div>
            <div class="frt mr20 change_type">
                <div class="type_item flt">
                    <span class="mr20">全部账号</span>
                    <span class="blue_font mr20">10</span>
                </div>
                <div class="type_item flt">
                    <span class="mr20">启用账号</span>
                    <span class="blue_font mr20">10</span>
                </div>
                <div class="type_item flt">
                    <span class="mr20">禁用账号</span>
                    <span class="red_font mr20">10</span>
                </div>
            </div>
        </div>   
    </div>
</template>
<script>
import table from '@/components/Table/table'
export default {
    data(){
        return { 
            search:"",
        }
    },
    components:{
        'table-counter':table
    },
    methods:{
        haClick(command){
             console.log(command)
        },
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')});
    },
    beforeMount(){
       
    },
    mounted(){   
        this.$nextTick(function () {
            this.$store.commit('showloadding',{show:false}); 
        })  
    }
}

</script>
<style scoped>
.el-dropdown-link,.el-dropdown {
    cursor: pointer;
    /* color: #409EFF; */
    padding: 5px;
    background-color: #f4f5f6;
    border: 1px solid #e5edf0;
    border-radius:4px;
}
.change_type {
    cursor: pointer;
    border: 1px solid #e5edf0;
    border-radius:4px;
    line-height: 28px;
}
.type_item { padding: 0 15px;}
.el-icon-arrow-down {
    font-size: 12px;
}
</style>


