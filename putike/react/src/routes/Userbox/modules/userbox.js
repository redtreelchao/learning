import fetch from 'isomorphic-fetch'

// ------------------------------------
// Constants
// ------------------------------------


// ------------------------------------
// Actions
// ------------------------------------



export const actions = {
  // increment,
  // doubleAsync,
  // fetchPosts,
}

// ------------------------------------
// Action Handlers
// ------------------------------------
const ACTION_HANDLERS = {
  // [COUNTER_INCREMENT]: (state, action) => state + action.payload,
  // [RECEIVE_POSTS]:(state,action)=>state + action.payload
}

// ------------------------------------
// Reducer
// ------------------------------------
const initialState = 0
export default function counterReducer (state = initialState, action) {
  const handler = ACTION_HANDLERS[action.type]

  return handler ? handler(state, action) : state
}
