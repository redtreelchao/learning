import fetch from 'isomorphic-fetch'
import {SEARCH_DONE,CAT_MORE,SEARCH_URL,DO_SORT} from '../../../constants'


export const doSearch = (keyword,pageNum=1) => {
  return dispatch => {
    return fetch(SEARCH_URL+pageNum+'&keyword='+keyword+'&source=1')
        .then(response => response.json())
        .then(json => dispatch({
          type: SEARCH_DONE,
          search_products:json,
          pageNum:1,
          keyword:keyword
        }))
  }
}

export const searchMore = (keyword,pageNum,sort) => dispatch => {
  // console.log(document.body.scrollHeight-newtop)
    return fetch(SEARCH_URL+pageNum+'&keyword='+keyword+'&source=1&sort='+sort)
        .then(response => response.json())
        .then(json => {
            return dispatch({
            type: CAT_MORE,
            search_products:json,
            pageNum:pageNum,
             })
        })
        // .then(json => console.log(json))
}

export const doSort=(keyword,sort)=>dispatch=>{
  return fetch(SEARCH_URL+'1&keyword='+keyword+'&source=1&sort='+sort)
        .then(response => response.json())
        .then(json => {
            return dispatch({
            type: DO_SORT,
            search_products:json,
            pageNum:1,
            keyword:keyword
             })
        })
}

export const actions = {
  doSearch,
  searchMore,
  doSort
}

// process.nextTick(framedone)

// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
   [DO_SORT]:(state,action)=>Object.assign({},state,{pageNum:action.pageNum},{keyword:action.keyword},{search_products:action.search_products.data&&action.search_products.data.products || []}),
   [SEARCH_DONE]:(state,action)=>Object.assign({},state,{pageNum:action.pageNum},{keyword:action.keyword},{search_products:action.search_products.data&&action.search_products.data.products || []}),
     // [SCROLL_UP]:(state,action)=>Object.assign({},state,{top:action.top}),
    [CAT_MORE]:(state,action)=>{
      if(!action.search_products.data){
        return Object.assign({},state,{isEnd:true})
      }
      var search_products = action.search_products&&action.search_products.data?[...state.search_products,...action.search_products.data.products]:[]
      // console.log(products)
      return Object.assign({},state,{search_products:search_products},{pageNum:action.pageNum})
     },
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = {search_products:[],pageNum:1,isEnd:false,keyword:''}
export default function homeReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
