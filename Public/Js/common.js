//判断邮箱格式
function isMail(email){
  var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
  return reg1.test( email );
}

//判断手机格式
function isPhone(tel){ 
     if(tel.length != 11){
        return 0;
     }
	 reg = /^13[0-9]{9}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}$/;
	 return reg.test( tel );
}

//向服务器发出请求
function loading(){
	$('.loading').show();
}

//请求完成
function complete_loading(){
	$('.loading').hide();
}

/*
 *   控制保留几位有效小数
 *   ValueString   小数
 *   nAfterDotNum  保留的小数位数
 */
function FormatAfterDotNumber( ValueString, nAfterDotNum ){
   var ValueString,nAfterDotNum ;
　　var resultStr,nTen;
　　ValueString = ""+ValueString+"";
　　strLen = ValueString.length;
　　dotPos = ValueString.indexOf(".",0);
　　if (dotPos == -1)
        {
　　　　resultStr = ValueString+".";
　　　　for (i=0;i<nAfterDotNum ;i++)
                {
　　　　　　resultStr = resultStr+"0";
　　        }
　　　　return resultStr;
　　}
　　else
        {
　　　　if ((strLen - dotPos - 1) >= nAfterDotNum ){
　　　　　　nAfter = dotPos + nAfterDotNum  + 1;
　　　　　　nTen =1;
　　　　　　for(j=0;j<nAfterDotNum ;j++){
　　　　　　　　nTen = nTen*10;
　　　　　　}
　　　　　　resultStr = Math.round(parseFloat(ValueString)*nTen)/nTen;
　　　　　　return resultStr;
　　　　}
　　　　else{
　　　　　　resultStr = ValueString;
　　　　　　for (i=0;i<(nAfterDotNum  - strLen + dotPos + 1);i++){
　　　　　　　　resultStr = resultStr+"0";
　　　　　　}
　　　　　　return resultStr;
　　　　}
　　}
} 

/*
 *   根据年、月获得当月最大天数
 *   year   年
 *   month  月
 */
function getDaysInMonth(year,month){
	month = parseInt(month,10);  //parseInt(number,type)这个函数后面如果不跟第2个参数来表示进制的话，默认是10进制。
	var temp = new Date(year,month,0);
	return temp.getDate();
}


/*
 *  判断是否不为字母或数字
 *  str  人判断的字符串
 */
function isLetNum(str){
	var reg = /^[A-Za-z0-9]*$/;
	return reg.test(str);
}

/*
*   判断是否为数字
*/
function isNum(num){
    var reg = /^\d+$/;
    return reg.test(num);
}

/*
    判断是否为qq

*/
function isQQ(num){
    var reg = /^[1-9]\d{4,10}$/;
    return reg.test(num);
}







