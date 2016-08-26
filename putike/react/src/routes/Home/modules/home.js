import fetch from 'isomorphic-fetch'

// ------------------------------------
// Constants
// ------------------------------------
// export const COUNTER_INCREMENT = 'COUNTER_INCREMENT'
// export const RECEIVE_POSTS = 'RECEIVE_POSTS'

import {PRODUCTS_URL,PAGE_DONE,GET_MORE} from '../../../constants'
// ------------------------------------
// Actions
// ------------------------------------


export const framedone = (pageNum) => {
  return dispatch => {
    return fetch(PRODUCTS_URL+pageNum)
        .then(response => {
          return response.json()
        })
        .then(json => dispatch({
          type: PAGE_DONE,
          products:json,
          pageNum:1
        }))
        .then(json => console.log(json))
  }
}

export const getMore = (pageNum) => dispatch => {
    return fetch(PRODUCTS_URL+pageNum)
        .then(response => response.json())
        .then(json => {
            return dispatch({
            type: GET_MORE,
            products:json,
            pageNum:pageNum,
             })
        })
        // .then(json => console.log(json))
  
}

export const actions = {
  framedone,
  getMore
}

// process.nextTick(framedone)

// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
     [PAGE_DONE]:(state,action)=>Object.assign({},state,{products:action.products.data.products},{pageNum:action.pageNum}),
     [GET_MORE]:(state,action)=>{
      if(!action.products.data){
         return Object.assign({},state,{isEnd:true})
      }
      var products = [...state.products,...action.products.data.products]
      // console.log(products)
      return Object.assign({},state,{products:products},{pageNum:action.pageNum})
     },
     // [SCROLL_UP]:(state,action)=>Object.assign({},state,{top:action.top}),
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = {products:[],pageNum:1,isEnd:false}
export default function homeReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
