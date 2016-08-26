import React from 'react'

import classes from './Payorder.scss'

import MainHeader from '../../public/MainHeader'

import Carousel from '../../public/Carousel'
import classname from 'classnames';

import GoBuy from '../../public/GoBuy'

var Payorder = React.createClass({
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
      title:'订单支付',
      showCoupons:false,
      discount:0,
      active:{},
      orderid:this.props.location.query.orderid,
      uid:this.props.location.query.uid,
      coupons:'',
      couponid:''
    }
  },
  componentDidMount:function(){
    // this.props.getDetail(6366);
    var orderid=this.state.orderid,uid=this.state.uid
    this.props.getOrderToPay(orderid,uid)
    this.props.getCoupons(uid)

  },
  showCp(){
    this.setState({showCoupons:true})
  },
  hideCp(){
     this.setState({showCoupons:false})
  },
  select_coupons(i,id,value){
    var active={};
    active[i]=1;
    this.setState({active:active,discount:value,couponid:id})

  },
  deSelectCoupons(){
    this.setState({discount:0,active:{},couponid:''})
  },
  couponsChange(event){
    this.setState({coupons:event.target.value,couponid:''})
  },
  addTicket(){
    return this.props.bindCoupons(this.state.coupons,this.state.uid)
  },
  // minusAmount(){
  //   // this.setState({amount:this.state.amount==1?1:this.state.amount-1})

  // },
  // addAmount(){
  //   this.setState({amount:this.state.amount+1})
  // },

  render: function () {
    let pic=this.props.payorder.paydetail&&this.props.payorder.paydetail.pic||''
    let total=this.props.payorder.paydetail&&this.props.payorder.paydetail.total||0
    let name=this.props.payorder.paydetail&&this.props.payorder.paydetail.title||''
    let detail=this.props.payorder.paydetail&&this.props.payorder.paydetail.data&&this.props.payorder.paydetail.data.details||[]
    let coupons=[];
    let llhref='http://pay.putike.cn/llpay/llpayapi.php?id='+this.state.orderid+'&coupon='+this.state.couponid
    let alihref='http://pay.putike.cn/alipay/alipayapi.php?id='+this.state.orderid+'&coupon='+this.state.couponid
    let wxhref='http://pay.putike.cn/wechat_pay/wechatapi.php?id='+this.state.orderid+'&coupon='+this.state.couponid
    // let wxhref
    let ticket=this.state.coupons;

    if (this.props.payorder.coupons==[]) {
      coupons=<div className={classes.info_list} ><span>优惠券</span><span className={classes.showmore} onClick={this.showCp}>0张可用</span></div>
    }else{
      if (this.state.showCoupons) {
          coupons=this.props.payorder.coupons.map((result,i)=>{
            var boundClick=this.select_coupons.bind(this,i,result.id,result.value);
            if (this.state.active[i]==1) {
            return (<div className={classes.info_list} onClick={this.deSelectCoupons} key={i+1}><span>优惠券 {result.value} 元</span><span className={classes.chosen}></span></div>)
            }else{
            return (<div className={classes.info_list} onClick={boundClick} key={i+1}><span>优惠券 {result.value} 元</span><span></span></div>)
            }
          })
        coupons.unshift(<div className={classes.info_list} key={0}><span>优惠券</span><span className={classes.showmore} onClick={this.hideCp}>{-this.state.discount||this.props.payorder.coupons&&this.props.payorder.coupons.length+'张优惠券'}</span></div>)
      }else{
        coupons.unshift(<div className={classes.info_list} key={0}><span>优惠券</span><span className={classes.showmore} onClick={this.showCp}>{-this.state.discount||this.props.payorder.coupons&&this.props.payorder.coupons.length+'张优惠券'}</span></div>)
      }
      }
    return (
         <div className={classes.payorderWrapper}>
            <MainHeader title={this.state.title}/>
          <div className={classes.product_body}>
            <div className={classes.info_block}>
              <div className={classes.info_wrapper}>
                <div className={classes.imgwrapper}>
                  <img src={pic}/>
                </div>
                <div className={classes.proc_info}>
                  <p>{name}</p>
                  <p className={classes.order_price}>￥ {total - this.state.discount}</p>
                </div>
                <div className="clear"></div>
              </div>
            </div>
            <div className={classes.info_block}>
              {detail.map((result,i)=>{
                return  <div className={classes.info_list} key={i}><span>{result.item}</span><span className={classes.right}>X {result.num}</span></div>
              })}  
            </div>
            <div className={classes.info_block}>
              {coupons}
              <div className={classes.info_list}><span><input type="text" placeholder="添加优惠券号" ref={(c) => this._input = c} value={ticket} onChange={this.couponsChange}/></span><span className={classes.addbtn} onClick={this.addTicket}>添加</span></div>
              
            </div>
            <div className={classes.info_block}>
              <div className={classes.info_list}><span>支付方式</span></div>
              <div className={classes.info_list}><a href={llhref}><i className={classes.pay_llpay}></i><span>银联支付</span><span className={classes.pay_road}></span></a></div>
              <div className={classes.info_list}><a href={alihref}><i className={classes.pay_alipay}></i><span>支付宝支付</span><span className={classes.pay_road}></span></a></div>
            </div>
            <div className={classes.tips}><p>温馨提示: 请在一小时内付款,如遇支付问题,请致电</p><p>400-600-800</p></div>
          </div>
         </div>
    )
  }
});





export default Payorder
