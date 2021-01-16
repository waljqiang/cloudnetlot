// 获取浏览器窗口的可视区域的高度
function getViewPortHeight() {
    return document.documentElement.clientHeight || document.body.clientHeight;
}

var computeOffset =  Math.floor((getViewPortHeight()-245)/25);
var globalPageOffset = computeOffset>10?computeOffset:10; //18;  //默认显示的分页个数

//MAC地址增加冒号
function add_mac_colon(macAdd) {
    var routerView_mac = "";
    if (macAdd.length == 12) {
        for (var g = 0; g < macAdd.length; g++) {
            var Mstr = (g + 1) % 2;
            if (Mstr == 0) {
                if (g == 11) {
                    routerView_mac += macAdd[g];
                } else {
                    routerView_mac += macAdd[g] + ":";
                }
            } else if (Mstr == 1) {
                routerView_mac += macAdd[g];
            }
        }
    }
    return routerView_mac
}

export {globalPageOffset,add_mac_colon};