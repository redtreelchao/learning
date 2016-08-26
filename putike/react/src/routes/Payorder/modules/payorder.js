import fetch from 'isomorphic-fetch'

// ------------------------------------
// Constants
// ------------------------------------
import {PAY_URL,ORDER_TO_PAY,GET_COUPONS,COUPONS_URL,BIND_COUPONS_URL,BIND_COUPONS} from '../../../constants'

// ------------------------------------
// Actions
// ------------------------------------

export const getOrderToPay =(id,uid)=>{
	return dispatch=> fetch(PAY_URL+'&id='+id+'&uid='+uid)
	.then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
          type: ORDER_TO_PAY,
          data:json.data
        })
      }else{
        alert(json.msg)
      }
    })
}


export const getCoupons =(uid)=>{
	return dispatch=> fetch(COUPONS_URL+'&uid='+uid)
	.then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
          type: GET_COUPONS,
          data:json.data
        })
      }else{
        alert(json.msg)
      }
    })
}

export const bindCoupons=(code,uid)=>{
  return dispatch=> fetch(BIND_COUPONS_URL+'&code='+code+'&uid='+uid)
  .then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
          type: BIND_COUPONS,
          data:json.data
        })
      }else{
        alert(json.msg)
      }
    })
}

export const actions = {
  getOrderToPay,
  getCoupons,
  bindCoupons
}



// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
	[ORDER_TO_PAY]:(state,action) => Object.assign({},state,{paydetail:action.data}),
	[GET_COUPONS]:(state,action)=> {
		if (!action.data) {
			return state
		}else{
			return Object.assign({},state,{coupons:action.data.coupons})
		}
	},
  [BIND_COUPONS]:(state,action)=> {
    if (!action.data) {
      return state
    }else{
      let coupons=[...state.coupons,action.data]
      return Object.assign({},state,{coupons})
    }
  }
  // [COUNTER_INCREMENT]: (state, action) => state + action.payload,
  // [RECEIVE_POSTS]:(state,action)=>state + action.payload
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = {coupons:[],paydetail:{}}
export default function counterReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
