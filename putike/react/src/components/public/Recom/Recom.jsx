import React from 'react'
import classes from './Recom.scss'
import {Link} from 'react-router'

import {KEYWORD_URL} from '../../../constants'

var Recom = React.createClass({
  getInitialState:function(){
    return {
      search:this.props.keyword||'',
      stylekeyword:[],
      citykeyword:[]
    }
  },
  getKeyword(){
    fetch(KEYWORD_URL)
        .then(response => response.json())
        .then(json=>{
          var stylekeyword=sessionStorage.stylekeyword=json.data&&json.data[0]
          var citykeyword=sessionStorage.citykeyword=json.data&&json.data[1]
          this.setState({stylekeyword:stylekeyword,citykeyword:citykeyword})
        })
  },
  componentDidMount:function(){
    var style=sessionStorage.stylekeyword
    var city=sessionStorage.citykeyword
    var stylekeyword,citykeyword;
    if (style==undefined||city==undefined) {
      this.getKeyword()
    }else{
      var stylekeyword=style.split(',');
      var citykeyword=city.split(',');
      this.setState({stylekeyword:stylekeyword,citykeyword:citykeyword})
    }
  },
  render: function () {
     var searchHistory=window.localStorage.searchHistory,historylist;
    
    var citykeyword=this.state.citykeyword;
    var stylekeyword=this.state.stylekeyword;
    var citylist,stylelist;

    //解决不能Link到当前页面
    if (this.props.from=='list') {
      citylist=citykeyword&&citykeyword.map((result,i)=>{
       return  <Link to={'/catemap?keyword='+result} key={i}><span>{result}</span></Link>
      })
       stylelist=stylekeyword&&stylekeyword[0]&&stylekeyword.map((result,i)=>{
        return  <Link to={'/catemap?keyword='+result} key={i}><span >{result}</span></Link>
       })
    if (searchHistory) {
      searchHistory=searchHistory.split(',')
      historylist= <div className={classes.history}>
              <p className={classes.title}><i className={classes.icon_play}></i>搜索历史</p>
              <div className={classes.tags}>
                {searchHistory.map((result,i)=><Link to={'/catemap?keyword='+result} key={i}><span >{result}</span></Link>)}
              </div>
            </div>
      }
    }else if(this.props.from=='map') {
        citylist=citykeyword&&citykeyword.map((result,i)=><Link to={'/catelist?keyword='+result} key={i}><span >{result}</span></Link>)
        stylelist=stylekeyword&&stylekeyword.map((result,i)=><Link to={'/catelist?keyword='+result} key={i}><span >{result}</span></Link>)
      if (searchHistory) {
      searchHistory=searchHistory.split(',')
      historylist= <div className={classes.history}>
              <p className={classes.title}><i className={classes.icon_play}></i>搜索历史</p>
              <div className={classes.tags}>
                {searchHistory.map((result,i)=><Link to={'/catelist?keyword='+result} key={i}><span >{result}</span></Link>)}
              </div>
            </div>
      }
    }
    return (
       <div className={classes.Catemap}>
         {historylist}
          <div className={classes.play}>
            <p className={classes.title}><i className={classes.icon_play}></i>大家都玩啥</p>
            <div className={classes.tags}>
              {citylist}
            </div>
          </div>
          <div>
             <p className={classes.title}><i className={classes.icon_play}></i>大家都去哪</p>
            <div className={classes.tags}>
            {stylelist}
            </div>
          </div>
        </div>
    );
  }
});



export default Recom
