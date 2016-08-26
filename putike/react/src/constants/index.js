
export const AJAX_TEST = 'AJAX_TEST'
// export const TEST_API='http://120.27.106.18:3000/api'
export const TEST_API='http://wx.redtravel.cn'

export const PAGE_DONE = 'PAGE_DONE'

export const GET_MORE = 'GET_MORE'

export const CAT_MORE = 'CAT_MORE'

export const SCROLL_UP = 'SCROLL_UP'

export const GET_DETAIL = 'GET_DETAIL'

export const SEARCH_DONE = 'SEARCH_DONE'

export const SHOW_SEARCH = 'SHOW_SEARCH'

export const DEL_INDENT='DEL_INDENT'

export const GO_INDENT='GO_INDENT'

export const ADD_AMOUNT='ADD_AMOUNT'

export const MINUS_AMOUNT='MINUS_AMOUNT'


export const ORDER_TO_PAY='ORDER_TO_PAY'

export const GET_COUPONS='GET_COUPONS'

export const GET_ORDER_LIST='GET_ORDER_LIST'

export const ORDER_DETAIL='ORDER_DETAIL'

export const DO_SORT='DO_SORT'

export const APPID='wx8f2d4ba8d79914d1'

export const GET_KEYWORD='GET_KEYWORD'

export const CLEAR_PRODUCT='CLEAR_PRODUCT'

export const BIND_COUPONS='BIND_COUPONS'

export const GET_HISTORY='GET_HISTORY'

var host;
// if (__DEV__) {
// 	host='http://192.168.254.21:8082'
// }
// if (__PROD__) {
	host='http://iapi.putike.cn'
// }

export const KEYWORD_URL=host+'/index/api?method=product&action=hotkeyword'

export const COUPONS_URL=host+'/index/api?method=user&action=ticketlist'
export const ORDER_LIST=host+'/index/api?method=user&action=orderlist'
export const BIND_COUPONS_URL=host+'/index/api?method=user&action=bind_coupons'
export const PAY_URL=host+'/index/api?method=order&action=detail'
export const SEARCH_URL=host+'/index/api?method=product&action=index&page='
export const PRODUCTS_URL =host+'/index/api?method=product&action=index&page='
export const DETAIL_URL=host+'/index/api?method=product&action=detail'

export const INDEX_API=host+'/index/api?'