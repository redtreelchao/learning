import React from 'react'
import classes from './UserLogin.scss'
import MainHeader from '../../public/MainHeader'
import Recom from '../../public/Recom'
import {INDEX_API} from '../../../constants'
// import { History } from 'react-router'

// export const Header = (props) => (
//   <div>
//     <div className={classes.header}>
//       <input type='text' />
//     </div>
//   </div>
// )

// Header.propTypes = {
//   // counter: React.PropTypes.number.isRequired,
//   // doubleAsync: React.PropTypes.func.isRequired,
//   // increment: React.PropTypes.func.isRequired
// }

export default class UserLogin extends React.Component{
   constructor(props){
    super(props);
    this.state={
        tel:'',
        valid:'',
        showValidText:'发送验证码',
        canClick:true
    }
   }
  static contextTypes={
    router: React.PropTypes.object.isRequired
  }
   componentDidMount(){
    
   }
   validTime=()=>{
      var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; 
       if(!myreg.test(this.state.tel)) 
       { 
           alert('请输入有效的手机号码！'); 
           return false; 
       }
    if (this.state.canClick===false) {
      return;
    }
    this.setState({canClick:false})
    var _this=this;
    var  lastTime=function(time){
          if(time>0&&_this.props.location.pathname=='/UserLogin'){
           time-=1;
           var text=time+'s后重发'
           console.log(text)
          _this.setState({showValidText:text})
          setTimeout(function(){
              return lastTime(time)
          },1000)
         }else{
             _this.setState({showValidText:'发送验证码',canClick:true})
          }
      }
   lastTime(60)
   }
   getValid= () =>{
       if(!this.state.canClick){
        return
       }
                this.validTime()
                 fetch(INDEX_API+'method=user&action=valid&mobile='+this.state.tel)
                       .then(response=>response.json())
                       .then(data=>{
                           if (data.success==true) {
                                         this.setState({showValid:true,alreadyValid:true})
                           }
                       })
   }
   telChange=(event)=>{
           this.setState({tel:event.target.value})
   }
   validChange=(event)=>{
           this.setState({valid:event.target.value})
   }
   doLogin=()=>{
       fetch(INDEX_API+"method=user&action=login&mobile="+this.state.tel+'&code='+this.state.valid+'&openid='+localStorage.openid)
           .then(response=>response.json())
           .then(data=>{
                     this.setState({loading:false})
                     if (data.success==true) {
                                   window.localStorage.uid=data.data.id
                                   console.log(window.localStorage.uid)
                                  this.context.router.push('/Userbox')
                     }else{
                                 alert(data.msg)
                                       
                     }
                           return data
                               
           })
   }
   render() {
    var tel=this.state.tel
    var validBtn;
    return (
        <div className={classes.loginbody}>
        <MainHeader title='用户登录'/>
        <div className={classes.loginbox}>
         <div className={classes.input_wrapper}>
            <p className={classes.mobile}><input type='text' onFocus={this.clearTel} onChange={this.telChange} placeholder='请输入手机号' value={tel}/></p>
            <span onClick={this.getValid} >{this.state.showValidText}</span>  
         </div>
        <div className={classes.input_wrapper}>
            <p className={classes.valid}><input type='text' onChange={this.validChange} placeholder='请输入验证码'/></p>
        </div>
      </div>
      <div className={classes.loginBtn}>
        <span onClick={this.doLogin}>
        登录
        </span>
      </div>
      </div>
    )
  }
}




// var UserLogin = React.createClass({
//   contextTypes: {
//     router: React.PropTypes.object.isRequired
//   },
//   getInitialState:function(){
//     return {
//     	type:'search'

//     }
//   },
//   hideSearch:function(e){
//   nPopupPost()
//             this.createOrder();
//         e.preventDefault();
//       return this.context.router.goBack()
//   },
//   componentDidMount:function(){
    
//   },
//   render: function () {
//     return (
//         <div>
//         <MainHeader title='用户登录'/>
		   
// 		  </div>
//     );
//   }
// })e{}lert(data.msgn
// nPopupPost()
//
