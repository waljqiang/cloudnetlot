<template>
    <el-container v-cloak >
        <el-header height="70px">
            <header-counter></header-counter>
        </el-header>
        <el-main class="login_main" :style="'background:url('+img_bg+') no-repeat;'" >
            <div class="login_conrainer"  >
                <el-scrollbar id="main_scrol" style="height:  100%;width: auto;">
                    <h2 class="align_c">{{$t('text.agreement1')}}</h2>
                    <div>
                        <h3>{{$t('text.agreement2')}}</h3>
                        <p>{{$t('text.agreement3')}}</p>
                        <p>{{$t('text.agreement4')}}</p>
                        <p>{{$t('text.agreement5')}}</p>
                        <p>{{$t('text.agreement6')}}</p>
                        <h3>{{$t('text.agreement7')}}</h3>
                        <p>{{$t('text.agreement8')}}</p>
                        <p>{{$t('text.agreement9')}}</p>
                        <h3>{{$t('text.agreement10')}}</h3>
                        <p>{{$t('text.agreement11')}}</p>
                        <p>{{$t('text.agreement12')}}</p>
                        <p>{{$t('text.agreement13')}}</p>
                        <p>{{$t('text.agreement14')}}</p>
                        <p>{{$t('text.agreement15')}}</p>
                        <p><span>{{$t('text.agreement16')}}</span></p>
                        <p><span>{{$t('text.agreement17')}}</span></p>
                        <p><span>{{$t('text.agreement18')}}</span></p>
                        <p><span>{{$t('text.agreement19')}}</span></p>
                        <p><span>{{$t('text.agreement20')}}</span></p>
                        <h3>{{$t('text.agreement21')}}</h3>
                        <p>{{$t('text.agreement22')}}</p>
                        <p>{{$t('text.agreement23')}}</p>
                        <p>{{$t('text.agreement24')}}</p>
                        <p>{{$t('text.agreement25')}}</p>
                        <p>{{$t('text.agreement26')}}</p>
                        <p>{{$t('text.agreement27')}}</p>
                        <p>{{$t('text.agreement28')}}</p>
                        <p>{{$t('text.agreement29')}}</p>
                        <p>{{$t('text.agreement30')}}</p>
                        <h3>{{$t('text.agreement31')}}</h3>
                        <p>{{$t('text.agreement32')}}</p>
                        <p>{{$t('text.agreement33')}}</p>
                        <h3>{{$t('text.agreement34')}}</h3>
                        <p>{{$t('text.agreement35')}}</p>
                        <h3>{{$t('text.agreement36')}}</h3>
                        <p>{{$t('text.agreement37')}}</p>
                        <p>{{$t('text.agreement38')}}</p>
                    </div>
                </el-scrollbar>
            </div>            
        </el-main>
        
    </el-container>
</template>
<script>

//引入图片
import Header from '@/projects/home/assets/Out_header/header.vue';
import img_bg from '@/projects/home/assets/login/login_bg.jpg';

export default {
    data(){
        return {
            loading_txt:"正在注册，请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",

            lang:this.$i18n.locale,
            img_bg:img_bg,  
            agreeUrl: "@/projects/home/lang/agreement_"+this.$i18n.locale+".html" 
        }
    },
    components:{
        'header-counter':Header
    },
    methods: {
        goLogin(){
            this.$router.push({ path: '/login' })  
        }
    },
    created(){

    },
    mounted(){
        
    },
    beforeDestroy() {
    
    }
}
</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
p{ line-height: 25px;}
p>span {padding-left: 24px;}
.login_conrainer { width: 850px; min-height: 400px; height: 80%; margin: 5% auto 0 auto; padding: 20px; overflow: hidden; border-radius: 5px; background: $white_bg;}
</style>