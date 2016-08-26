import React from 'react'
import classes from './GoBuy.scss'


var GoBuy = React.createClass({
  getInitialState:function(){
    return {
    }
  },
  componentDidMount:function(){
  //   fetch(this.props.link,{method:'POST',mode:"cors"
  //   })
  //   .then(response=> response.json())
  //   .then((value)=> {
  //     this.setState({
  //       photo:value.data.banner
  //     })
  //     return value
  //   })
  },
  render: function () {
    var price = this.props.price;
    var qi='';
    if (this.props.step==0) {
      qi='起'
    }
    return (
	  	<div className={classes.bottom_bar}>
	  		<div className={classes.price}>￥ {price} {qi}</div>
	  		<div className={classes.buybtn} onClick={this.props.nextStep}>{this.props.title}</div>
	  	</div>
    );
  }
});



export default GoBuy
