import fetch from 'isomorphic-fetch'

// ------------------------------------
// Constants
// ------------------------------------
import {PAY_URL,ORDER_DETAIL,GET_COUPONS,COUPONS_URL} from '../../../constants'

// ------------------------------------
// Actions
// ------------------------------------

export const getOrderDetail =(id,uid)=>{
	return dispatch=> fetch(PAY_URL+'&id='+id+'&uid='+uid)
	.then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
          type: ORDER_DETAIL,
          data:json.data
        })
      }else{
        alert(json.msg)
      }
    })
}



export const actions = {
  getOrderDetail

}

// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
	[ORDER_DETAIL]:(state,action) => Object.assign({},state,{orderdetail:action.data})
  // [COUNTER_INCREMENT]: (state, action) => state + action.payload,
  // [RECEIVE_POSTS]:(state,action)=>state + action.payload
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = {orderdetail:{}}
export default function counterReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
