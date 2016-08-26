import React from 'react'
import classes from './Catemap.scss'
import Header from '../../public/Header'
import Recom from '../../public/Recom'
// import { History } from 'react-router'

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

var Catemap = React.createClass({
  contextTypes: {
    router: React.PropTypes.object.isRequired
  },
  getInitialState:function(){
    return {
    	type:'search'
    }
  },
  hideSearch:function(e){
        e.preventDefault();
      return this.context.router.goBack()
  },
  handleChange:function(event) {

  },
  handleSearch:function(value){
      var searchHistory=window.localStorage.searchHistory
      if (searchHistory) {
        searchHistory=searchHistory.split(',')
        searchHistory.push(value)
        window.localStorage.searchHistory=searchHistory=searchHistory.slice(-6)
      }else{
        searchHistory=[value]
      }
      
     window.localStorage.searchHistory=searchHistory
      return this.context.router.push('/catelist?keyword='+value)
  },
  componentDidMount:function(){
    var keyword=this.props.location.query.keyword
    if (keyword) {
      this.context.router.push('/catelist?keyword='+keyword)
    }
    
  },
  render: function () {
    return (
        <div>
       <Header type={this.state.type} hideSearch={this.hideSearch} doSearch={this.handleSearch}/>
		    <Recom from='map'/>
		  </div>
    );
  }
});

export default Catemap