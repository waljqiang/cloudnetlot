<template>
    <div class="out_header">
        <div class="flt">
            <img :src="img_logo" style="vertical-align:middle" />
        </div>
        <div class="frt">
            <!-- <div class="header_txt_menu cur">云平台</div>|
            <div class="header_txt_menu cur">开放平台</div>|
            <div class="header_txt_menu cur">后台管理</div>|
            <div class="header_txt_menu cur">帮助文档</div> -->
        </div>
    </div> 
</template>
<script>
import img_logo from '../login/logo.png';

export default {
    data(){
        return {
            img_logo:img_logo,
        }
    },
    
}
</script>
<style scoped>

</style>