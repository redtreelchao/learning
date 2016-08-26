import Product from '../Product'
import React from 'react'
import classes from './Products.scss'

export const Products = (props) => {
  return (
    <div>
      {props.list.map((result,i) => {
          // return <img src={result.pic} key={i}/>
          return <Product product={result} key={i}/>
         })}
    </div>
)}

// Product.propTypes = {
// 	products:React.PropTypes.array.isRequired
//   // counter: React.PropTypes.number.isRequired,
//   // doubleAsync: React.PropTypes.func.isRequired,
//   // increment: React.PropTypes.func.isRequired
// }

export default Products
