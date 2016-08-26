import React from 'react'
import ReactDOM from 'react-dom'
import createBrowserHistory from 'history/lib/createBrowserHistory'
import {applyRouterMiddleware, Router, useRouterHistory } from 'react-router'
import useScroll from 'react-router-scroll';
import { syncHistoryWithStore } from 'react-router-redux'
import createStore from './store/createStore'
import { Provider } from 'react-redux'

import DevTools from './extends';

// import  FastClick from 'fastclick';
// FastClick.attach(document.body);


var devTool='';

if(__DEV__){
  devTool=<DevTools />
}

// console.log(__APIURL__)

const MOUNT_ELEMENT = document.getElementById('root')

// Configure history for react-router
const browserHistory = useRouterHistory(createBrowserHistory)({
  basename: __BASENAME__
})

// Create redux store and sync with react-router-redux. We have installed the
// react-router-redux reducer under the key "router" in src/routes/index.js,
// so we need to provide a custom `selectLocationState` to inform
// react-router-redux of its location.
// window.__INITIAL_STATE__是将应用第一次渲染交给后台，减少首屏渲染时间
const store = createStore(window.__INITIAL_STATE__, browserHistory)
// const store = createStore({counter:1,counter2:100}, browserHistory)
const history = syncHistoryWithStore(browserHistory, store, {
  selectLocationState: (state) => state.router
})

let render = (key = null) => {
  const routes = require('./routes/index').default(store)
  const App = (
    <Provider store={store}>
      <div style={{ height: '100%' }}>
        <Router history={history} children={routes} key={key} render={applyRouterMiddleware(useScroll())}/>
        {devTool}
      </div>
    </Provider>
  )
  ReactDOM.render(App, MOUNT_ELEMENT)
}
// Enable HMR and catch runtime errors in RedBox
// This code is excluded from production bundle
if (__DEV__ && module.hot) {
  const renderApp = render
  const renderError = (error) => {
    const RedBox = require('redbox-react')

    ReactDOM.render(<RedBox error={error} />, MOUNT_ELEMENT)
  }
  render = () => {
    try {
      renderApp(Math.random())
    } catch (error) {
      renderError(error)
    }
  }
  module.hot.accept(['./routes/index'], () => render())
}

// Use Redux DevTools chrome extension
if (__DEBUG__) {
  if (window.devToolsExtension) window.devToolsExtension.open()
}

render()



