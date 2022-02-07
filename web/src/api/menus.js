import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/menu/list',
    method: 'get',
    params
  })
}

export function add(data) {
  return request({
    url: '/menu/add',
    method: 'POST',
    data
  })
}

export function edit(data) {
  return request({
    url: '/menu/edit',
    method: 'POST',
    data
  })
}

export function del(params) {
  return request({
    url: '/menu/delete',
    method: 'GET',
    params
  })
}
