import React from 'react'
import classes from './Carousel.scss'
import Slider  from 'react-slick'
import fetch from 'isomorphic-fetch'

const { array, func, object, string } = React.PropTypes

var Carousel = React.createClass({
  getInitialState:function(){
    return {
      settings:{
      dots: false,
      infinite: true,
      speed: 500,
      arrows:false,
      autoplay:true
    }
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
    var photos = this.props.photos ||[]
    let width=document.body.clientWidth
    let height=Math.floor(document.body.clientWidth*210/375)
    let divstyle={
      width:width,
      height:height,
    }
    return (
        
        <Slider {...this.state.settings} style={divstyle}>
           {photos.map((result,i) => {
          return <img src={result.pic+'!/both/'+width+'x'+height} key={i}/>
         })}
        </Slider>
    );
  }
});

// export default Carousel


// import carousel from './Carousel.js'
//
//
// class Carousel extends React.Component{
//     constructor(props){
//       super(props)
//       this.state={
//         settings:{
//               dots: true,
//               infinite: true,
//               speed: 500,
//               slidesToShow: 1,
//               slidesToScroll: 1
//             }
//       }
//       this.getPics=this.getPics.bind(this)
//        console.log(this.state)
//        console.log(this.photo)
//   }
//   getPics(){
//     fetch("http://120.27.106.18:3000/api",{method:'POST',mode:"cors"
//     })
//     .then(response=> response.json())
//     .then((value)=> {
//       this.setState({
//         photo:value.data.banner
//       })
//       return value
//     })
//   }
//   render(){
//      return (<div>
//               <Slider {...this.state.settings}>
//               {this.state.photo.map((result,i) => {
//                return <img src={result.pic} key={i}/>
//               })}
//               </Slider>
//           </div>)
//   }
// }



export default Carousel
