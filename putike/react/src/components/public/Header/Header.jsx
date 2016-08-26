import React from 'react'
import classes from './Header.scss'
import personal from './image/i_personal.png'
import deletecorn from './image/i_delete.png'
import { Link } from 'react-router'


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

var Header = React.createClass({
  getInitialState:function(){
    return {
    	search:this.props.keyword||''
    }
  },
  handleChange:function(event) {
    // this.setState({value: event.target.value});
    this.setState({search:event.target.value})
  },
  clearInput:function(){
    this.setState({search:''})
     this._input.focus();
  },
  upenter:function(e){
    e.preventDefault();
    if(e.keyCode==13){
      this.props.doSearch(this.state.search);
    }
  },
  componentDidMount:function(){
    if(this.props.type=='search'){
      this._input.focus();
    }
  },
  render: function () {
  	var search = this.state.search,rightBar,del_icon='';
  
  	if(this.props.type!='home'){
  		rightBar=<span className={classes.cancel} onClick={this.props.hideSearch}>取消</span>
      if (this.state.search!="") {
        del_icon=<img src={deletecorn}  className={classes.delete_icon} onClick={this.clearInput}/>
      }
  	}else{
		  rightBar=<Link to={'/userbox'}><span className={classes.personal}><img src={personal} /></span></Link>
        	}
    return (
        <div>
		    <div className={classes.header}>
		      <div className={classes.inputwrapper}>
            <input type='text' ref={(c) => this._input = c} value={search} onChange={this.handleChange}  placeholder='搜索' onFocus={this.props.showSearch} onKeyUp={this.upenter}/>
            {del_icon}
          </div>
            {rightBar}
		    </div>
        <div className={classes.headerWrapper}></div>
		  </div>
    );
  }
});

export default Header
