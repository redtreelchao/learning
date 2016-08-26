import fetch from 'isomorphic-fetch'

// ------------------------------------
// Constants
// ------------------------------------
import {GET_ORDER_LIST,ORDER_LIST} from '../../../constants'

// ------------------------------------
// Actions
// ------------------------------------



export const getOrderlist =(uid)=>{
	return dispatch=> fetch(ORDER_LIST+'&uid='+uid)
	.then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
          type: GET_ORDER_LIST,
          list:json.data
        })
      }else{
        alert(json.msg)
      }
    })
}

export const actions = {
	getOrderlist
}

// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
	[GET_ORDER_LIST]:(state,action)=> Object.assign({},state,{list:action.list}),
  // [COUNTER_INCREMENT]: (state, action) => state + action.payload,
  // [RECEIVE_POSTS]:(state,action)=>state + action.payload
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = {list:null}
export default function orderReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
