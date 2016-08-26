import React from 'react'

import classes from './MyCoupons.scss'

import MainHeader from '../../public/MainHeader'


var Mycoupons = React.createClass({
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
      title:'我的优惠券',
      showAddCoupon:false,
      title_right:'兑换',
      uid:window.localStorage.uid
    }
  },
  componentDidMount:function(){
    this.props.getCoupons(this.state.uid);

  },
  showAddInput(){
    this.setState({showAddCoupon:true,title_right:'取消'})
  },
  hideAddInput(){
    this.setState({showAddCoupon:false,title_right:'兑换'})
  },
  // minusAmount(){
  //   // this.setState({amount:this.state.amount==1?1:this.state.amount-1})

  // },
  // addAmount(){
  //   this.setState({amount:this.state.amount+1})
  // },

  render: function () {
    let count=this.props.mycoupons.coupons&&this.props.mycoupons.coupons.count||0
    let list=this.props.mycoupons.coupons&&this.props.mycoupons.coupons.coupons||[]
    let addCoupon;
    if (this.state.showAddCoupon) {
      addCoupon=<div className={classes.addCoupon}>
      <input type='text' placeholder="请输入优惠券号"/>
      <span>添加</span>
      <div className={classes.shadow}></div>
      </div>
    }
    return (
        <div>
          <MainHeader title={this.state.title} doRight={this.showAddInput} title_right={this.state.title_right} cancelRight={this.hideAddInput}/>
          {addCoupon}
          <div className={classes.product_body}>
            <div  className={classes.line}><p>{count}张可用券</p></div>
            {list.map((result,i)=>{
              var startTime=new Date(result.start*1000).toLocaleDateString();
              var endTime=new Date(result.end*1000).toLocaleDateString();
              if (result.state==1) {
                return  (<div className={classes.info_block} key={i}>
                <div className={classes.inner_block}>
                  <p className={classes.coupon_value}>
                    ￥{result.value}
                  </p>
                  <p className={classes.coupon_info}>有效期 {startTime} 至 {endTime} </p>
                   <p className={classes.coupon_info}>该券适用于美宿所有产品 </p>
                </div>
              </div>)
              }else{
                  let res;
                   if (result.timestate==4) {
                    res= (<p className={classes.coupon_info}>已过期 </p>)
                  }else{
                    res= (<p className={classes.coupon_info}>已使用 </p>)
                  }
                
                  return  (<div className={classes.info_block_used} key={i}>
                <div className={classes.inner_block_used}>
                  <p className={classes.coupon_value}>
                    ￥{result.value}
                  </p>
                  <p className={classes.coupon_info}>有效期 {startTime} 至 {endTime} </p>
                   {res}
                </div>
              </div>)
              }
              
            })}

          </div>
        </div>
    )
  }
});


export default Mycoupons
