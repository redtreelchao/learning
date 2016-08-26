import React from 'react'
import classes from './Catelist.scss'
import Products from '../../public/Products'
import Header from '../../public/Header'
import Sequence from '../../public/Sequence'
import Dropload from '../../public/Dropload'
import Recom from '../../public/Recom'

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


var Catelist = React.createClass({
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
      open:false,
      type:'',
      keyword:this.props.location.query.keyword,
      sort:this.props.location.query.sort
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
  hideSearch:function(e){
    e.preventDefault();
     this.context.router.push('/')
  },
  sort_p:function(){
    if (this.state.sort!='p') {
       this.props.doSort(this.state.keyword,'p')
       this.setState({sort:'p'})
    }
  },
  sort_h:function(){
     if (this.state.sort!='_h') {
      this.props.doSort(this.state.keyword,'_h')
       this.setState({sort:'_h'})
    }
       
  },
  handleScroll:function (){
      return this.props.searchMore(this.state.keyword,this.props.catelist.pageNum+1)
    },
  componentDidMount:function(){
    if (this.props.catelist.keyword!=this.state.keyword) {
      this.props.doSearch(this.props.location.query.keyword,1);
    }
  },
  // componentDidUpdate:function(){
  //   console.log(111)
  // },
  render: function () {
    var showBody,dropLoad,products=this.props.catelist.search_products,keyword=this.state.keyword;
      if (products.length==0) {
        // this.context.router.push('/catemap?keyword='+this.state.keyword)
        dropLoad='';
        showBody=<div><div className={classes.no_result}><p>没有符合您要求的产品，试试别的吧</p></div><Recom from='list'/></div>
      }else{
         showBody=<div><Sequence sort_p={this.sort_p} sort_h={this.sort_h}/><Products list={products}/></div>;
         if (this.props.catelist.isEnd==true||products.length<10) {
            dropLoad= <Dropload  handleScroll={this.handleScroll} isEnd={true}/>
          }else{
            dropLoad=<Dropload  handleScroll={this.handleScroll}/>
          }
      }
      
    return (
      <div className={classes.homewrapper}>
      <Header type={this.state.type} showSearch={this.showSearch} hideSearch={this.hideSearch} keyword={keyword}/>
				{showBody}
          {dropLoad}
	  		  </div>)
  }
});

export default Catelist


