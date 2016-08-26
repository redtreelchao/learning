import React from 'react'
import classes from './MainHeader.scss'
import { Router, RouterContext, Link, browserHistory } from 'react-router';


var MainHeader = React.createClass({
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
    }
  },

  goBack:function(e) {
    e.preventDefault();
    // this.handleShowSearch();
    this.context.router.push('/catemap')
  },
  componentDidMount:function(){

  },
  componentWillUnmount() {
 
  },
 render:function() {
     var title_right=this.props.title_right,right;
      if (title_right=='取消') {
        right=<p className={classes.title_cancel_right} onClick={this.props.cancelRight}>{title_right}</p>
      }else{
        right=<p className={classes.title_bar_right} onClick={this.props.doRight}>{title_right}</p>
      }
      switch (title_right){
        case '取消':right=<p className={classes.title_cancel_right} onClick={this.props.cancelRight}>{title_right}</p>;
        break;
        case 'tel':right=<p className={classes.title_tel_right} onClick={this.props.doRight}><a href='tel:400-600-800'></a></p>
        break;
        case 'coupon':right=<p className={classes.title_bar_right} onClick={this.props.doRight}>{title_right}</p>
        break;
      }
    return (
      <div>
    <div className={classes.MainHeader}>
      <i className={classes.goBack} onClick={this.context.router.goBack}></i>
      <p className={classes.title}>{this.props.title}</p>
       {right}
    </div>
  </div>
    )
  }
});



export default MainHeader
