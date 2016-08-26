import React from 'react'
import classes from './Dropload.scss'


var Dropload = React.createClass({
  getInitialState:function(){
    return {
      isLockDown:false,
    }
  },
  handleScroll(){
    var oHeight=document.body.offsetHeight;
    var sHeight=document.body.scrollHeight;
    var sTop=document.body.scrollTop;
    if(!this.state.isLockDown&&sHeight-oHeight-sTop<=40){
      this.setState({isLockDown:true})
      return this.props.handleScroll()
    }
  },
  componentDidMount:function(){
    window.addEventListener('scroll',this.handleScroll); 
  },
  componentWillReceiveProps(){
    console.log(this.state.isLockDown)
    if (!this.props.isEnd) {
      this.setState({isLockDown:false})
    }
  },
  componentWillUnmount() {
   window.removeEventListener('scroll', this.handleScroll);
  },
  render: function () {
    var text;
    if(this.props.isEnd){
      text=<p className={classes.loadEnd}>已全部加载</p>
      window.removeEventListener('scroll', this.handleScroll);
    }else{
      text=<p className={classes.loadNotEnd}>正在努力加载</p>
    }
    return (
      <div>
        <div className={classes.infinite_load}>
          {text}
        </div>
      </div>
    );
  }
});


export default Dropload