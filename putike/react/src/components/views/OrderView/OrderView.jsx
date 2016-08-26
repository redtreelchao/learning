import React from 'react'

import classes from './OrderView.scss'

import MainHeader from '../../public/MainHeader'

import {INDEX_API} from '../../../constants'

var OrderView = React.createClass({
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
      title:'订单详情',
      refund_text:'以上都不是？请写下您的退款理由',
      buttonstatus:'申请退款',
      step:0,
      showReasons:false,
      active:{0:0,1:0,2:0,3:0},
      reason:[],
      orderid:this.props.location.query.orderid,
      uid:this.props.location.query.uid
    }
  },
  refundFocus(){
    if (this.state.refund_text=='以上都不是？请写下您的退款理由') {
      this.setState({refund_text:''})
    }  
  },
  choiceTag(i,result){
    var active=this.state.active;
    var reason=this.state.reason;
    active[i]=1;
    reason[i]=result;
    this.setState({active:active,reason:reason})
  },
  dischoiceTag(i){
      var active=this.state.active;
    var reason=this.state.reason;
    active[i]=0;
    reason[i]='';
    this.setState({active:active,reason:reason})
  },
  doRefund(){
    var reason=this.state.reason.join()
    var refund_text=this.state.refund_text=='以上都不是？请写下您的退款理由'?'':this.state.refund_text
     fetch(INDEX_API+"method=order&action=refund&order="+this.state.orderid+'&uid='+this.state.uid+'&remark='+reason+refund_text)
    .then(response=>response.json())
    .then(data=>{
      if (data.success==true) {
         this.context.router.push('/orderlist?uid='+this.state.uid)
      }else{
        alert(data.msg)
      }
      return data
    })
  },
  nextStep(){
    var step=this.state.step;
    switch(step){
      case 0:this.setState({buttonstatus:'提交',step:1,showReasons:true});
      break;
      case 1:this.doRefund();
      break;
    }
  },
  refundBlur(){
    if (this.state.refund_text=='') {
      this.setState({refund_text:'以上都不是？请写下您的退款理由'})
    }
  },
  cancelRefund(){
    this.setState({showReasons:false,step:0,buttonstatus:'申请退款',active:{0:0,1:0,2:0,3:0},reason:[]})
  },
  refundInput(event){
    this.setState({refund_text:event.target.value})
  },
  componentDidMount:function(){
    // this.props.getDetail(6366);
      var orderid=this.props.location.query.orderid,uid=this.props.location.query.uid
      this.props.getOrderDetail(orderid,uid)
  },
  // minusAmount(){
  //   // this.setState({amount:this.state.amount==1?1:this.state.amount-1})

  // },
  // addAmount(){
  //   this.setState({amount:this.state.amount+1})
  // },

  render: function () {
    let orderid=this.props.order.orderdetail.order||'';
    let intro = this.props.order.orderdetail.intro&&this.props.order.orderdetail.intro.split('\r\n')||'';
    let orderStatus={1:'支付后待确认',2:'待支付',3:'订单支付成功，未预约',4:'支付成功,发出预约等酒店确认',5:'预订失败',8:'酒店回传确认，预约成功',9:'订单完结',10:'退款申请',11:'退订退款完成',12:'拒绝退订',16:'部分退款'}
    let introlist;
    if (intro) {
      introlist= (intro.map((result,i)=><li key={i}> {result}</li>))
    }
    let orderdetail=this.props.order.orderdetail,refundBtn;
    if (orderdetail.stat==3||orderdetail.stat==4||orderdetail.stat==8) {
      refundBtn=<div className={classes.askrefund} onClick={this.nextStep}>{this.state.buttonstatus}</div>
    }
    let reason = ['酒店无房，要求退款','买贵了，后悔了','计划有变，去不了','重新买']
    let refund_text=this.state.refund_text;
    let refundwrapper,shadow;
    if (this.state.showReasons) {
       shadow=<div className={classes.shadow}></div>
        refundwrapper=  <div className={classes.refund_wrapper}>
          <div className={classes.info_block}>
              <div className={classes.porduct_title}>
                <p className={classes.main_title}>/选择退款原因/</p>
                <span className={classes.closebtn} onClick={this.cancelRefund}>取消</span>
              </div>
              <div className={classes.refund_tag}>
               {reason.map((result,i)=>{                              
                                if (this.state.active[i]==1){
                                  var boundClick=this.dischoiceTag.bind(this,i,result);
                                  return <p key={i} onClick={boundClick} className={classes.red}>{result}</p>
                                }else{
                                   var boundClick=this.choiceTag.bind(this,i,result);
                                  return <p key={i} onClick={boundClick}>{result}</p>
                                }
                              })}
                <div className="clear"></div>
              </div>
              <div className={classes.refund_text}>
                <textarea  value={refund_text} onFocus={this.refundFocus} onChange={this.refundInput} onBlur={this.refundBlur}/>
              </div>
            </div>
        </div>
    }
    let hotellist;
    if (orderdetail.hotel) {
      hotellist=orderdetail.hotel.map((result,i)=>{
        let rooms = result.rooms;
        let roomlist=rooms.map((result,j)=>{
          return (<div className={classes.info_list} key={j}><span>第 {j+1} 份</span><span className={classes.use_status}>未使用</span><span className={classes.right}><a href='tel:400-600-800'>电话预约</a></span></div>)
        })
         return (<div className={classes.info_block} key={i}>
                      <div className={classes.info_list}><span className={classes.info_title}>{result.itemname}</span><span className={classes.right}>￥ {result.total}</span></div>
                        {roomlist}
                    </div>)
      })
    }
    return (
        <div>
        {shadow}
          <div className={classes.orderWrapper}>
            <MainHeader title={this.state.title}/>
          <div className={classes.product_body}>
            <div className={classes.info_block}>
              <div className={classes.order_status}><span>订单号 {orderid}</span><span className={classes.right}>{orderStatus[orderdetail.stat]}</span></div>
              <div className={classes.info_wrapper}>
                <div className={classes.imgwrapper}>
                  <img src={orderdetail.pic} />
                </div>
                <div className={classes.proc_info}>
                  <p>{orderdetail.title}</p>
                  <p className={classes.subtitle}>{orderdetail.name}</p>
                </div>
                <div className="clear"></div>
              </div>
            </div>
            <div className={classes.porduct_content}>
              <ul>
                {introlist}
              </ul>
          </div>
            {hotellist}
            <div className={classes.info_block}>
              <div className={classes.info_list}><span className={classes.info_title}>联系方式</span></div>
              <div className={classes.info_list}><span>{orderdetail.contact}</span><span className={classes.right}>{orderdetail.tel}</span></div>

            </div>
          </div>
          {refundwrapper}
            
            {refundBtn}
          </div>
        </div>
    )
  }
});


export default OrderView
