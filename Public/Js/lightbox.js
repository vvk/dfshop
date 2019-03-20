var lightBox=function(){
    //调用该构造器的原型方法init(),初始化灯箱效果
    this.init.apply(this,arguments);
}

//灯箱构造器原型对象
lightBox.prototype={
    //灯箱构造器初始化方法
    init:function(id){
        //显示层
        if(!id && !(typeof id=== "string")){
			 return false;
		}	
		
        this.box=document.getElementById(id);  //获取灯箱框		

        this.box.style.zIndex=10001;   //设置覆盖的z轴坐标，确保位于上面
        this.box.style.position="fixed";    //绝对定位显示
        this.box.style.display='none';     //初始化为隐藏
        
        //覆盖层
        this.lay=document.body.insertBefore(document.createElement("div"),document.body.childNodes[0]);   //创建一个div元素
        this.lay.style.display='none';   //初始化为隐藏显示
        this.lay.style.backgroundColor="#000";   //设置背景色为黑色
        this.lay.style.zIndex=10000;    //设置覆盖的z轴，确保位于显示层的下方
        this.lay.style.position='fixed';   //以固定定位显示
        this.lay.style.left=0;
        this.lay.style.top=0;
        this.lay.style.width='100%';
        this.lay.style.height='100%';
        if(document.all){  //设置覆盖层透明度，兼容IE
            this.lay.style.filter="alpha(opacity:60)";
        }else{   //兼容FF
            this.lay.style.opacity=0.6;
        }
    },
    //显示灯箱
    show:function(options){
        this.lay.style.display="block";     //显示灯筱覆盖层
        this.box.style.display="block";      //显示灯箱
        var top=document.documentElement.scrollTop - this.box.offsetHeight/2;      //居中定位
        var left=document.documentElement.scrollLeft - this.box.offsetWidth/2;      // 居中定位
        //预防图像过大，影响效果
        //如果图像高度不是很大，则可以考虑居中定位
        if(top > -300){
            this.box.style.marginTop = document.documentElement.scrollTop - this.box.offsetHeight/2 + "px";
            this.box.style.top="50%";
        }else{      //否则不在居中显示
            this.box.style.top="50px";
        }
        //如果图像不是很大，则可以居中显示
        if(left > -512){
            this.box.style.marginLeft = document.documentElement.scrollLeft - this.box.offsetWidth/2 + "px";
            this.box.style.left="50%";
        }else{     //否则不在居中显示
            this.box.style.left="20px";
        }
    },
    //关闭灯箱
    close:function(){
        this.box.style.display="none";        //隐藏灯箱
        this.lay.style.display="none";        //隐藏覆盖层
    }    
};
