import { injectReducer } from '../../store/reducers'

export default (store) => ({
  path:'Catemap',
  /*  Async getComponent is only invoked when route matches   异步获取组件*/
  getComponent (nextState, cb) {
    /*  Webpack - use 'require.ensure' to create a split point
        and embed an async module loader (jsonp) when bundling   */
    require.ensure([], (require) => {
      /*  Webpack - use require callback to define
          dependencies for bundling   */
      const CatemapView = require('./containers/CatemapContainer').default
      const reducer = require('./modules/catemap').default

      /*  Add the reducer to the store on key 'counter'  */
      injectReducer(store, { key: 'catemap', reducer })

      /*  Return getComponent   */
      cb(null, CatemapView)

    /* Webpack named bundle   */
    }, 'CatemapView')
  }
})
