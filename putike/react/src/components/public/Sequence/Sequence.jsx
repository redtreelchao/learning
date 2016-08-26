import React from 'react'
import classes from './Sequence.scss'


export const Sequence = (props) => {
  return (
    <div>
      <div className={classes.seqtype}>
      	<a onClick={props.sort_h}>
      		<span>按推荐度</span>
      	</a>
        <span>|</span>
      	<a onClick={props.sort_p}>
      		<span>按价格<i></i></span>
      	</a>
      </div>
    </div>
)}



export default Sequence
