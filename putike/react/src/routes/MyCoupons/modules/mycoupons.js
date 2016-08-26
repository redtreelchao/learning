import fetch from 'isomorphic-fetch'

// ------------------------------------
// Constants
// ------------------------------------
import {COUPONS_URL,GET_COUPONS} from '../../../constants'

// ------------------------------------
// Actions
// ------------------------------------




export const getCoupons =(user)=>{
	return dispatch=> fetch(COUPONS_URL+'&uid='+user)
	.then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
          type: GET_COUPONS,
          coupons:json.data
        })
      }else{
        alert(json.msg)
      }
    })
}

export const actions = {
	getCoupons
}

// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
	[GET_COUPONS]:(state,action)=> Object.assign({},state,{coupons:action.coupons}),
  // [COUNTER_INCREMENT]: (state, action) => state + action.payload,
  // [RECEIVE_POSTS]:(state,action)=>state + action.payload
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = {coupons:[]}
export default function orderReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
