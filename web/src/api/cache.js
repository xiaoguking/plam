import request from '@/utils/request'

export function getCache(params) {
  return request({
    url: '/cache/getCache',
    method: 'get',
    params
  })
}
export function deleteCache() {
  return request({
    url: '/cache/deleteCache',
    method: 'get'
  })
}
