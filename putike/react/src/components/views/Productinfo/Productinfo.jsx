import React from 'react'

import classes from './Productinfo.scss'

import MainHeader from '../../public/MainHeader'

import Carousel from '../../public/Carousel'

import GoBuy from '../../public/GoBuy'

import fetch from 'isomorphic-fetch'

import {INDEX_API} from '../../../constants'

import Loading from '../../public/Loading'

import {wxLoginMixin} from '../../../mixins'


var Productinfo = React.createClass({
  mixins:[wxLoginMixin],
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
      title:'产品详情',
      loading:false,
      goBuy_title:'选择套餐',
      price:0,
      active:{},
      step:0,
     historyActive:{},
      title_right:'tel',
      name:window.localStorage.name||'',
      tel:window.localStorage.tel||'',
      valid:'',
      alreadyValid:false,
      showValid:false
    }
  },
  componentDidMount:function(){
    this.props.getDetail(this.props.location.query.id);
    this.props.getHistory(localStorage.uid)
  },
  componentWillUnmount:function(){
    this.props.clearProduct()
  },
  getValid(){
    if (!localStorage.uid) {
      fetch(INDEX_API+'method=user&action=valid&mobile='+this.state.tel)
      .then(response=>response.json())
      .then(data=>{
        if (data.success==true) {
          this.setState({showValid:true,alreadyValid:true})
        }
      })
    }
  },
  createOrder(){
    this.setState({loading:true})
    var order = this.props.productinfo.order;
    var neworder=[]
    for(let i=0,orderLength=order.length;i<orderLength;i++){
      if (order[i]) {
        neworder=[...neworder,order[i]]
      }
    }
    var product=JSON.stringify({"Num":neworder});
    fetch(INDEX_API+'method=order&action=create&uid='+localStorage.uid+'&mobile='+this.state.tel+'&product='+product+'&id='+this.props.productinfo.product.id+'&contact='+this.state.name)
    .then(response=>response.json())
    .then(data=>{
      this.setState({loading:false})
      if (data.success==true) {
         window.localStorage.name=this.state.name||''
         window.localStorage.tel=this.state.tel||''
        let orderid= data.data&&data.data.order
        this.context.router.push('/payorder?orderid='+orderid+'&uid='+localStorage.uid)
      }else{
        alert(data.msg)
      }
    })
  },
  doLogin(){
    this.setState({loading:true})
    fetch(INDEX_API+"method=user&action=login&mobile="+this.state.tel+'&code='+this.state.valid+'&openid='+localStorage.openid)
    .then(response=>response.json())
    .then(data=>{
      this.setState({loading:false})
      if (data.success==true) {
          window.localStorage.uid=data.data.id
          this.createOrder();
      }else{
        alert(data.msg)
      }
      return data
    })
  },
  init_order(){
    this.setState({goBuy_title:'选择套餐',step:0});
  },
  nameChange(event){
    this.setState({name:event.target.value})
  },
  telChange(event){
    this.setState({tel:event.target.value})
  },
  mailChange(event){
    this.setState({mail:event.target.value})
  },
  validChange(event){
    this.setState({valid:event.target.value})
  },
  clearName(){
    this.setState({name:''})
  },
  getHistory(){
      this.setState({step:9})
  },
  clearTel(){
    this.setState({tel:''})
  },
  // handleImgLoaded(){
  //   this.setState({loading:false})
  // },
  nextStep(){
    switch(this.state.step){
      case 0 : this.setState({goBuy_title:'下一步',step:this.state.step+1});
      break;
      case 1 :
       if (this.props.productinfo.order.length==0) {
        alert('请选择产品')
      }else{
       this.setState({goBuy_title:'立即支付',step:this.state.step+1});
      }
      break;
      case 2 :localStorage.uid?this.createOrder():this.state.alreadyValid?this.doLogin():this.getValid();
      break;
      case 9:this.createOrder();
      break;
    }
  },
  // minusAmount(){
  //   // this.setState({amount:this.state.amount==1?1:this.state.amount-1})
 
  // },
  // addAmount(){
  //   this.setState({amount:this.state.amount+1})
  // },
  select_room(i,price,code){
    // console.log(code)
    var active=this.state.active;
    if (active[i]==1) {
      active[i]=0
      this.setState({active:active})
      return this.props.delIndent(code,i)
    }else{
      active[i]=1
      this.setState({active:active})
      return this.props.goIndent(code,i,1,price)
    }
    // this.setState({active:i,price:price,order:1})
  },
  selectHistory(i,name,mobile){
    // console.log(code)
    var historyActive={};
      historyActive[i]=1
      this.setState({historyActive:historyActive,name:name,tel:mobile})
    // this.setState({active:i,price:price,order:1})
    },
     createMarkup(content) {
    return {__html: content};
  },
  render: function () {
    var info=this.props.productinfo.product;
    var content=  <div className={classes.porduct_content}>
      <div dangerouslySetInnerHTML={this.createMarkup(info.content)} />
    </div>;
    var price=0;
    if(this.state.step==0){
      price = this.state.price||info.price
    }else{
      for(let res in this.props.productinfo.order){
        price+=this.props.productinfo.order[res]['num']*this.props.productinfo.order[res]['price']||0
      }
    }
    var popup=info&&info.room||[]
     var rules=info.rule&&info.rule.split('\r\n')||[]
     var refund=info.refund&&info.refund.split('\r\n')||[]
    var popContainer,shadow=<div className={classes.shadow}></div>;
    var product_title=info&&info.title
    var name=this.state.name||''
    var tel=this.state.tel||''
    var product_name=info&&info.name
    var product_intro=info&&info.intro&&info.intro.split('\r\n')||[];
    var userlist,load;
    var historyUser=this.props.productinfo.history;
    if (!this.props.productinfo.fetchdone||this.state.loading) {
      load=<Loading />
    }else{
      load=''
    }
    if (this.state.showValid) {
      userlist=<div className={classes.room}>
                <ul className={classes.user_info}>
                <li><span>姓名</span><input type='text' onFocus={this.clearName} onChange={this.nameChange} placeholder='请输入姓名' value={name}/></li>
                <li><span>手机</span><input type='text' onFocus={this.clearTel} onChange={this.telChange} placeholder='请输入手机号' value={tel}/></li>
                <li><span>验证码</span><input type='text' onChange={this.validChange} placeholder='请输入验证码'/></li>
              </ul>
              </div>
    }else{
      userlist=<div className={classes.room}>
                <ul className={classes.user_info}>
                <li><span>姓名</span><input type='text' onFocus={this.clearName} onChange={this.nameChange} placeholder='请输入姓名' value={name}/></li>
                <li><span>手机</span><input type='text' onFocus={this.clearTel} onChange={this.telChange} placeholder='请输入手机号' value={tel}/></li>
             </ul>
              </div>
    }
    switch(this.state.step){
      case 0:popContainer=<div>
            <div className={classes.order_body}>

            <GoBuy price={price} nextStep={this.nextStep} title={this.state.goBuy_title} step={this.state.step}/>
            </div>
           </div>;
           shadow='';
      break;
      case 1:popContainer=   <div className={classes.create_order}>
            <div className={classes.order_body}>
               <div className={classes.info_block}>
              <div className={classes.porduct_title}>
                <p className={classes.main_title}>/套餐选择/</p>
                <i className={classes.closebtn} onClick={this.init_order}></i>
              </div>
              <div className={classes.room}>
                <ul>
                    {popup.map((result,i) =>{
                    var boundClick=this.select_room.bind(this,i,result.price,result.code);
                    if (this.state.active[i]==1) {
                      var addAmount = this.props.addAmount.bind(this,i,result.code)
                       var minusAmount = this.props.minusAmount.bind(this,i,result.code)
                      return <li key={i} className={classes.active}><div onClick={boundClick}><span>{result.name}</span> <span className={classes.right}>￥ {result.price}</span></div>
                        <div className={classes.room_detail}>
                          <p>{result.intro}</p>
                        </div>
                        <div className={classes.room_detail,classes.buynum}>
                          <span>购买数量</span>
                          <span className={classes.input_num}><span onClick={minusAmount}>-</span><span className={classes.numwrapper}>{this.props.productinfo.order[i]&&this.props.productinfo.order[i]['num']}</span><span onClick={addAmount}>+</span></span>
                        </div>
                      </li>
                    }else{
                      return <li key={i} onClick={boundClick}><div><span>{result.name}</span> <span className={classes.right}>￥ {result.price}</span></div></li>
                    }
                  })}
                </ul>
              </div>
            </div>
            <GoBuy price={price} nextStep={this.nextStep} title={this.state.goBuy_title} step={this.state.step}/>
            </div>
           </div>
      break;
      case 2:popContainer=  <div className={classes.create_order}>
            <div className={classes.order_body}>
              <div className={classes.info_block}>
              <div className={classes.porduct_title}>
                <p className={classes.user_info_title}>/联系人信息/</p>
                {localStorage.uid===undefined?'':<span className={classes.history_user} onClick={this.getHistory}>历史联系人</span>}
                <i className={classes.closebtn} onClick={this.init_order}></i>
                <div className='clear'></div>
                </div>
              {userlist}
            </div>
            <GoBuy price={price} nextStep={this.nextStep} title={this.state.goBuy_title} step={this.state.step}/>
            </div>
           </div>
      break;
      case 9:popContainer=   <div className={classes.create_order}>
            <div className={classes.order_body}>
               <div className={classes.info_block}>
              <div className={classes.porduct_title}>
                <p className={classes.user_info_title}>/历史联系人/</p>
               <i className={classes.closebtn} onClick={this.init_order}></i>
                <div className='clear'></div>
               </div>
              <div className={classes.room}>
                <ul>
                    {historyUser.map((result,i) =>{
                        if(this.state.historyActive[i]){
                            return <li key={i} className={classes.active}><div><span>{result.name}</span> <span className={classes.right}>{result.mobile}</span></div></li>
                        }else{
                    var boundClick=this.selectHistory.bind(this,i,result.name,result.mobile);
                      return <li key={i} onClick={boundClick}><div><span>{result.name}</span> <span className={classes.right}>{result.mobile}</span></div></li>
                        }
                  })}
                </ul>
              </div>
            </div>
            <GoBuy price={price} nextStep={this.nextStep} title={this.state.goBuy_title} step={this.state.step}/>
            </div>
           </div>
        break;
      default:'';
    }
    // var popup = this.props.productinfo.product.room.map((result,i) => {
    //       // return <img src={result.pic} key={i}/>
    //       return <Product product={result} key={i}/>
    //      })

    // console.log(typeof(content))

    return (
        <div>
          {load}
          {shadow}
          <MainHeader title={this.state.title} title_right={this.state.title_right} opacity={0}/>
          <div className={classes.product_body}>
            <Carousel photos={this.props.productinfo.product.img} />
            <div className={classes.info_block}>
              <div className={classes.porduct_title}>
                <p className={classes.main_title}>{product_title}</p>
                <p className={classes.sub_title}>{product_name}</p>
              </div>
              <div className={classes.porduct_content}>
                <ul>
                  {product_intro.map((result,i)=> <li key={i}>{result}</li>)}
                </ul>
              </div>
            </div>
            <div className={classes.info_block}>
              <div className={classes.porduct_title}>
                <p className={classes.main_title}>/美宿故事/</p>
              </div>
              {content}
            </div>
            <div className={classes.info_block}>
              <div className={classes.porduct_title}>
                <p className={classes.main_title}>/预订须知/</p>
              </div>
              <div className={classes.porduct_content}>
                <div>
                  <p>使用说明</p>
                  <ul className={classes.rules}>
                  {rules.map((result,i) =>{
                      return <li key={i}>{result}</li>
                  })}
                </ul>
                </div>
                <div>
                   <p>改退规则</p>
                  <ul className={classes.refund}>
                  {refund.map((result,i) =>{
                      return <li key={i}>{result}</li>
                  })}
                </ul>
                </div>
              </div>
            </div>
            
           
           {popContainer}
          </div>
        </div>
    )
  }
});


export default Productinfo
