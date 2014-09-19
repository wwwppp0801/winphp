
String.prototype.escapeHTML=function(){
    return this.replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\ /g, "&nbsp;");
}
$.fn.extend({
    smartbox: (function(){
        var DEFAULT_PARAMS={
            //载入数据的url，word会替换为输入框的当前值
            url:'${word}',
            dataType:'json',//could be 'json' or 'xhr'
            tipsRowRender:function(data){
                return '<div>'+ data.escapeHTML() + '</div>';
            },
            inputRender:function(data){
                return data;
            },
            dataProcess:function (json){
                return json;
            },
            mouseoverClass:'focus'
        };
        
        return function(params){
            if(typeof params=='undefined'){
                return this.data('smartbox.env');
            }
            var settings = jQuery.extend({}, DEFAULT_PARAMS, params),
                _form=this.parents('form').eq(0),
                tipsContainer=settings.tipsContainer,input=this;
            var env={currentFocus:0,
                curWord:'',
                prevWord:'',
                data:[],
                isVisible:false};
            input.data('smartbox.env',env);
            this.keyup(function(e){
                if((e.keyCode==40||e.keyCode==38)&&!env.isVisible&&env.data){
                    show();
                    return;
                }
                if(e.keyCode==40){//down
                    select(env.currentFocus+1);
                }else if(e.keyCode==38){//up
                    select(env.currentFocus-1);
                }else if(e.keyCode==13 || e.keyCode===0){
                    e.stopPropagation();
                    e.preventDefault();
                    env.curWord=input.val();
                    env.prevWord=env.curWord;
                    if(env.isVisible){
                        hide();
                    }
                }else{
                    env.curWord=input.val();
                }
            }).focus(function(){
                listen();
            }).blur(function(){
                stop_listen();
            }).click(function(e){
                e.stopPropagation();
            }).keydown(function(e){
                if(e.keyCode==13 || e.keyCode===0){
                    e.stopPropagation();
                    e.preventDefault();
                }
            });
            
            
            function startLoad(){
                if (env.curWord!==false&&env.curWord.trim().length!==0) {
                    var url=settings.url.replace(/\$\{word\}/g,encodeURIComponent(env.curWord));
                    if(settings.dataType=='json'){
                        $.ajax({
                            dataType:"jsonp",
                            url:url+(url.indexOf('?')==-1?'?':'&')+'callback=?', 
                            success:function(json){
                                input.trigger('smartbox.recieve',[input,json]);
                                env.data = settings.dataProcess(json);
                                show();
                            }
                        });
                    }else{
                        $.get(url,function(text){
                            input.trigger('smartbox.recieve',[input,text]);
                            env.data = settings.dataProcess(text);
                            show();
                        });
                    }
                }else{
                    hide();
                }
            }
            function hide(){
                tipsContainer.css('visibility', "hidden");
                env.isVisible = false;
                env.currentFocus=0;
                $(document).unbind('click',hide);
            }
            var timer=null;
            function listen(){
                if(timer!==null){
                    return;
                }
                timer = setTimeout(function(){
                    if (env.prevWord!=env.curWord) {
                        env.prevWord=env.curWord;
                        startLoad();
                    }
                    timer = setTimeout(arguments.callee, 100);
                }, 100);
             }
            
            function stop_listen(){
                if (timer) {
                    clearTimeout(timer);
                    timer = null;
                }
            }
            
            function select(sel,mouse){
                if(!env.data||env.data.length===0){return;}
                if (env.currentFocus > 0) {
                    tipsContainer.children().eq(env.currentFocus-1).removeClass(settings.mouseoverClass);
                }
                env.currentFocus = (sel<0?sel+env.data.length+1:sel)%(env.data.length+1);
                
                if (env.currentFocus > 0) {
                    var i=env.currentFocus - 1;
                    if(mouse!==true){
                        input.val(settings.inputRender(env.data[i]));
                    }
                    tipsContainer.children().eq(i).addClass(settings.mouseoverClass);
                    input.trigger("smartbox.select",[env.data[i],i]);
                }
                else {
                    if(mouse!==true){
                        input.val(env.prevWord);
                    }
                }
            }
            
            function show(){

                if (!env.data || env.data.length === 0||env.curWord.trim()=='') {
                    hide();
                    return;
                }
                input.trigger('smartbox.beforeShow');

                var html='';
                
                
                for (var i = 0; i < env.data.length; i++) {
                    html+=settings.tipsRowRender(env.data[i]);
                }

                tipsContainer.html(html);
                tipsContainer.children().each(function(i,elem){
                    $(elem).click(function(e){
                        select(i+1);
                        hide();
                        return false;
                    }).mouseover(function(event){
                        select(i+1,true);
                    });
                });
                $(document).click(
                    function(e){
                        if($(e.target).parents("form").get(0)!==_form.get(0)){
                            hide();
                        }
                        e.stopPropagation();
                    }
                );
                tipsContainer.css('visibility', 'visible');
                env.isVisible = true;
                input.trigger('smartbox.afterShow');
            }
            return this;
        };
    })()
});
