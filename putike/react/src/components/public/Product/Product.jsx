import React from 'react'
import classes from './Product.scss'
import { Link } from 'react-router'
import LazyLoad from 'react-lazy-load';

export const Product = (props) => {
  let cover=props.product&&props.product.pic
  let title=props.product&&props.product.title
  let name=props.product&&props.product.name
  let price=props.product&&props.product.price
  let id=props.product&&props.product.id
  let url="/productinfo?id="+id
  let width=document.body.clientWidth
  return (
    <div>
      <div className={classes.product_container} >
        
           <Link to={url}> <div className={classes.price_cover}><div className={classes.product_price}>￥{price}起</div></div></Link>
            <div>
            <LazyLoad height={200}>
         <img  alt={name} className={classes.product_cover} src={cover+'!/fw/'+width}/>
      </LazyLoad>
            </div>
            <div className={classes.product_info}>
                <p className={classes.product_title}> <Link to={url}>{title}</Link></p>
                <p className={classes.product_name}><Link to={url}>{name}</Link></p>
            </div>
           
       </div>
  
    </div>
)}

Product.propTypes = {
  // counter: React.PropTypes.number.isRequired,
  // doubleAsync: React.PropTypes.func.isRequired,
  // increment: React.PropTypes.func.isRequired
}

export default Product


  // <div>
  //   <h2 className={classes.counterContainer}>
  //     Counter:
  //     {' '}
  //     <span className={classes['counter--green']}>
  //       {props.counter}
  //     </span>
  //   </h2>
  //   <button className='btn btn-default' onClick={props.increment}>
  //     Increment
  //   </button>
  //   {' '}
  //   <button className='btn btn-default' onClick={props.doubleAsync}>
  //     Double (Async)
  //   </button>
  // </div>