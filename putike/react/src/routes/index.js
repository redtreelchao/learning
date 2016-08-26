// We only need to import the modules necessary for initial render
import CoreLayout from '../layouts/CoreLayout/CoreLayout'
import Home from './Home'
import Catemap from './Catemap'
import Catelist from './Catelist'
import Userbox from './Userbox'
import Productinfo from './Productinfo'
import Payorder from './Payorder'
import Order from './Order'
import Orderlist from './Orderlist'
import MyCoupons from './MyCoupons'
import UserLogin from './UserLogin'

/*  Note: Instead of using JSX, we recommend using react-router
    PlainRoute objects to build route definitions.   */

export const createRoutes = (store) => ({
  path: '/',
  component: CoreLayout,
  indexRoute: Home(store),
  childRoutes: [
    Catemap(store),
    Catelist(store),
    Userbox(store),
    Productinfo(store),
    Payorder(store),
    Order(store),
    Orderlist(store),
    MyCoupons(store),
    UserLogin(store)
  ]
})
 
/*  Note: childRoutes can be chunked or otherwise loaded programmatically
    using getChildRoutes with the following signature:

    getChildRoutes (location, cb) {
      require.ensure([], (require) => {
        cb(null, [
          // Remove imports!
          require('./Counter').default(store)
        ])
      })
    }

    However, this is not necessary for code-splitting! It simply provides
    an API for async route definitions. Your code splitting should occur
    inside the route `getComponent` function, since it is only invoked
    when the route exists and matches.
*/

export default createRoutes
