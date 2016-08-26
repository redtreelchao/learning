import React from 'react'
import classes from './Userbox.scss'
import MainHeader from '../../public/MainHeader'
import { Link } from 'react-router'



export const Userbox = (props) => {
let uid =window.localStorage.uid,list;
    if(uid===undefined){
        list=(
    <ul className={classes.userList}>
        <li className={classes.content_item}><p><Link to={'/UserLogin'}>我的订单</Link></p></li>
    	<li className={classes.preferential}><p><Link to={'/UserLogin'}>我的优惠券</Link></p></li>
    </ul>
        )
    }else{
        list=(
    <ul className={classes.userList}>
            <li className={classes.content_item}><p><Link to={'/orderlist?uid='+uid}>我的订单</Link></p></li>
    	<li className={classes.preferential}><p><Link to={'/mycoupons?uid='+uid}>我的优惠券</Link></p></li>
    
    </ul>
        )
    }
return (
  <div>
    <MainHeader title='个人中心'/>
   {list}
  </div>
)}

export default Userbox
