import { injectReducer } from '../../store/reducers'

export default (store) => ({
  path: 'payorder',
  /*  Async getComponent is only invoked when route matches   异步获取组件*/
  getComponent (nextState, cb) {
    /*  Webpack - use 'require.ensure' to create a split point
     and embed an async module loader (jsonp) when bundling   */
    require.ensure([], (require) => {
      /*  Webpack - use require callback to define
       dependencies for bundling   */
      const Payorder = require('./containers/PayorderContainer').default
      const reducer = require('./modules/payorder').default

      /*  Add the reducer to the store on key 'counter'  */
      injectReducer(store, { key: 'payorder', reducer })

      /*  Return getComponent   */
      cb(null, Payorder)

      /* Webpack named bundle   */
    }, 'Payorder')
  }
})