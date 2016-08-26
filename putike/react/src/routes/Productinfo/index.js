import { injectReducer } from '../../store/reducers'

export default (store) => ({
  path: 'productinfo',
  /*  Async getComponent is only invoked when route matches   异步获取组件*/
  getComponent (nextState, cb) {
    /*  Webpack - use 'require.ensure' to create a split point
     and embed an async module loader (jsonp) when bundling   */
    require.ensure([], (require) => {
      /*  Webpack - use require callback to define
       dependencies for bundling   */
      const Productinfo = require('./containers/ProductContainer').default
      const reducer = require('./modules/productinfo').default

      /*  Add the reducer to the store on key 'counter'  */
      injectReducer(store, { key: 'productinfo', reducer })

      /*  Return getComponent   */
      cb(null, Productinfo)

      /* Webpack named bundle   */
    }, 'Productinfo')
  }
})
