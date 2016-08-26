import fetch from 'isomorphic-fetch'

// ------------------------------------
// Constants
// ------------------------------------
import {GET_HISTORY,INDEX_API,DETAIL_URL,GET_DETAIL,GO_INDENT,ADD_AMOUNT,MINUS_AMOUNT,DEL_INDENT,CLEAR_PRODUCT} from '../../../constants'

// ------------------------------------
// Actions
// ------------------------------------


export const getDetail= (id)=>{
  return dispatch=>{
    return fetch(DETAIL_URL+'&id='+id)
    .then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
          type: GET_DETAIL,
          product:json.data
        })
      }else{
        alert(json.msg)
      }
    })
  }
}


export const getHistory= (uid)=>{
  return dispatch=>{
    return fetch(INDEX_API+'method=user&action=contact&uid='+uid)
    .then(response => response.json())
    .then(json => {
      if(json.success){
        return dispatch({
         type:GET_HISTORY,
          history:json.data
        })
      }else{
        alert(json.msg)
      }
    })
  }
}

export const clearProduct=()=>{
  return dispatch=>{
    return dispatch({
      type:CLEAR_PRODUCT
    })
  }
}

export const goIndent=(code,index,num,price)=>{
  return dispatch=>{
    return dispatch({
      type:GO_INDENT,
      num:num,
      index:index,
      code:code,
      price
    })
  }
}

export const delIndent=(code,index)=>{
  return dispatch=>{
    return dispatch({
      type:DEL_INDENT,
      index:index,
    })
  }
}

export const addAmount=(index,code)=> dispatch=> dispatch({
  type:ADD_AMOUNT,
  index:index,
  code,
})

export const minusAmount=(index,code)=> dispatch=> dispatch({
  type:MINUS_AMOUNT,
  index:index,
  code,
})


// export const framedone = (pageNum) => {
//   return dispatch => {
//     return fetch(productsURL+pageNum)
//       .then(response => response.json())
//       .then(json => dispatch({
//         type: PAGE_DONE,
//         products:json
//       }))
//       .then(json => console.log(json))
//   }
// }

export const actions = {
  // increment,
  // doubleAsync,
  // fetchPosts,
  getDetail,
  goIndent,
  addAmount,
  minusAmount,
  clearProduct,
  getHistory
}

// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
    [GET_HISTORY]:(state,action)=>Object.assign({},state,{history:action.history}),
  [GET_DETAIL]:(state,action) => Object.assign({},state,{product:action.product},{fetchdone:true}),
  [CLEAR_PRODUCT]:(state,action)=>Object.assign({},state,{product:[]}),
  [DEL_INDENT]:(state,action)=>{
    var order=state.order,num=state.order[action.index]['num'];
    order[action.index]['num']=0
    return Object.assign({},state,{order:order})
  },
  [GO_INDENT]:(state,action) => {
    // var active=[...state.active,...action.active]
     var order=state.order;
     order[action.index]={'code':action.code,'num':action.num,'price':action.price}
     // var order=[...state.order,{'code':action.code,'num':action.num}];

     return Object.assign({},state,{order:order})
  },
  [ADD_AMOUNT]:(state,action) => {
    var order=state.order;
    order[action.index]['num']+=1;
    return Object.assign({},state,{order:order})
  },
  [MINUS_AMOUNT]:(state,action)=>{
    var order=state.order;
    if(order[action.index]['num']==1){
      order[action.index]['num']=1;
    }else{
      order[action.index]['num']-=1;
    }
    return Object.assign({},state,{order:order})
  }
  // [COUNTER_INCREMENT]: (state, action) => state + action.payload,
  // [RECEIVE_POSTS]:(state,action)=>state + action.payload
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = {history:[],product:{},order:[],fetchdone:false}
export default function counterReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
