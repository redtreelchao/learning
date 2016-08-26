import {INDEX_API,APPID} from '../constants'

export var wxLoginMixin={
  componentWillMount:function(){
    var uid=window.localStorage.uid
    var refer=window.location.href
    var href=encodeURI('http://meisu.putike.cn/imeisu/'+'?redirect='+refer)
    var ua = navigator.userAgent.toLowerCase();  

    if(ua.match(/MicroMessenger/i)=="micromessenger"&&localStorage.openid==undefined) {
      if (this.props.location.query.openid&&uid===undefined) {
           fetch(INDEX_API+'method=user&action=wxlogin&openid='+this.props.location.query.openid)
            .then(response=>response.json())
            .then(data=>{
              if (data.success==true) {
                if (data.data!==null) {
                  window.localStorage.uid=data.data&&data.data.id
                }
                window.localStorage.openid=this.props.location.query.openid
              }else{
                // alert(data['msg'])
              }
            })
            // let response = await fetch(INDEX_API+'method=user&action=wxlogin&openid='+this.props.location.query.openidf);
            // let data = response.json();
            // console.log(data);
      }else{
        let toWx='https://open.weixin.qq.com/connect/oauth2/authorize?appid='+APPID+'&redirect_uri='+href+'&response_type=code&scope=snsapi_base&state=123#wechat_redirect'
          window.location.href=toWx;
      }
        return true;  
    } else {  
        return false;  
    } 
  }
}