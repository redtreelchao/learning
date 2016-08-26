import React from 'react'

import {Link} from 'react-router'
import classes from './Orderlist.scss'

import MainHeader from '../../public/MainHeader'


var Orderlist = React.createClass({
    contextTypes: {
        router: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {
            title: '我的订单',
        }
    },
    componentDidMount: function() {
        var uid=this.props.location.query.uid
        this.props.getOrderlist(uid);

    },
    // minusAmount(){
    //   // this.setState({amount:this.state.amount==1?1:this.state.amount-1})

    // },
    // addAmount(){
    //   this.setState({amount:this.state.amount+1})
    // },

    render: function() {
        let list = this.props.orderlist.list||[]
        let listBody
        let orderStatus={1:'支付后待确认',2:'待支付',3:'订单支付成功，未预约',4:'支付成功,发出预约等酒店确认',5:'预订失败',8:'酒店回传确认，预约成功',9:'订单完结',10:'退款申请',11:'退订退款完成',12:'拒绝退订',16:'部分退款'}
        if (list) {
          if (list.length!=0) {
          listBody=list.map((result,i)=>{
                return (
                    <div className={classes.info_block} key={i}>
                      <div className={classes.order_status}><span>订单号 {result.order}</span><span className={classes.right}>{orderStatus[result.stat]}</span></div>
                      <div className={classes.info_wrapper}>
                        <div className={classes.imgwrapper}>
                          <Link to={'/order?orderid='+result.order+'&uid='+this.props.location.query.uid}>
                           <img src={result.pic+'!/fw/125'}/>
                           </Link>
                        </div>
                        <div className={classes.proc_info}>
                          <p className={classes.maintitle}>
                           <Link to={'/order?orderid='+result.order+'&uid='+this.props.location.query.uid}>
                          {result.title}
                          </Link>
                          </p>
                          <p className={classes.subtitle}>
                           <Link to={'/order?orderid='+result.order+'&uid='+this.props.location.query.uid}>
                          {result.name}
                          </Link>
                          </p>
                        </div>
                        <div className="clear"></div>
                      </div>
                    </div>
                )
              })
          }else{
            listBody=<div className={classes.empty_box}>
              <p >你还没有订单哦</p>
            </div>
          }
        }
       
        return (
          <div>
          <div className={classes.listWrapper}>
            <MainHeader title={this.state.title}/>
          <div className={classes.product_body}>
            {listBody}
          </div>
          <Link to="/"><div className={classes.home_btn}><span>去首页看看</span></div></Link>
          </div>
          
        </div>
        )
    }
});


export default Orderlist
