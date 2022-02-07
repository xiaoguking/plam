import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/log/list',
    method: 'get',
    params
  })
}

export function del(params) {
  return request({
    url: '/log/delete',
    method: 'get',
    params
  })
}
