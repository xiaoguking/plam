import request from '@/utils/request'

export function getRouter() {
  return request({
    url: '/user/getRouter',
    method: 'get'
  })
}
