import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/shielding/List',
    method: 'get',
    params
  })
}

export function save(data) {
  return request({
    url: '/shielding/save',
    method: 'post',
    data
  })
}

export function del(data) {
  return request({
    url: '/shielding/delete',
    method: 'post',
    data
  })
}
