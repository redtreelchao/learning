import React from 'react'
import classes from './HomeView.scss'
import Products from '../../public/Products'
import Header from '../../public/Header'
import Dropload from '../../public/Dropload'
import BaiduEvent from '../../public/BaiduEvent'
import {wxLoginMixin} from '../../../mixins'
// import {Motion, spring} from 'react-motion';


// class HomeView extends React.Component {
// 	constructor(props){
// 		super(props)
// 		process.nextTick(props.framedone)
// 		window.addEventListener('scroll',this.props.getMore())
// 	}
// 	render(){
// 	  return (<div className={classes.homewrapper} onScroll={this.props.getMore}>
// 				<Products list={this.props.home.products}/>
// 	  		  </div>)
// 	}
// }



var HomeView = React.createClass({
   mixins:[wxLoginMixin],
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
      open:false,
      type:'home',
      tongji_event:{}
    }
  },
  // handleShowSearch:function() {
  //   this.setState({open: true,type:'search'});
  //   document.body.style.overflow='hidden';
  // },
  // handleHideSearch:function() {
  //   this.setState({open: false,type:''});
  //   document.body.style.overflow='scroll';
  // },
  showSearch:function(e) {
    e.preventDefault();
    // this.handleShowSearch();
    this.context.router.push('/catemap')
  },
  // hideSearch:function(e){
  //   e.preventDefault();
  //   this.handleHideSearch();
  // },
  handleScroll:function (){
      return this.props.getMore(this.props.home.pageNum+1)
    },
  componentDidMount:function(){
    if (this.props.home.pageNum==1) {
      this.props.framedone(this.props.home.pageNum);
    }
  },
  tongjiShow(tongjiEvent){
    this.setState({tongji_event:tongjiEvent})
  },
  componentWillUnmount() {
	},
  render: function () {
    var showBody,dropLoad;
    // if(this.state.type=='search'){
    //   showBody=<div></div>;
    // }else{
      showBody=<Products list={this.props.home.products}/>;
    // }
    if (this.props.home.isEnd==true) {
      dropLoad= <Dropload  handleScroll={this.handleScroll} isEnd={true}/>
    }else{
      dropLoad=<Dropload  handleScroll={this.handleScroll} />
    }
    return (
      <div className={classes.homewrapper} onClick={this.tongjiShow}>
        <Header type={this.state.type} showSearch={this.showSearch}/>
       
			   {showBody}
         {dropLoad}
         <BaiduEvent tongji_event={this.state.tongji_event}/>
	  		  </div>)
       }
});

export default HomeView



  // <Motion style={{x: spring(this.state.open ? 100 : 0)}}>
  //         {({x}) =>
  //           <div className={classes.search}  style={{
  //               WebkitTransform: `translate3d(${x}%, 0, 0)`,
  //               transform: `translate3d(${x}%, 0, 0)`,
  //             }}>
  //             <Catemap />
  //             </div>
             
  //         }
  //       </Motion>


