import request from '@/utils/request'

export function adminSave(data) {
  return request({
    url: '/system/adminSave',
    method: 'POST',
    data
  })
}

export function getAdminSystem() {
  return request({
    url: '/system/getadmin',
    method: 'get'
  })
}

export function IndexSave(data) {
  return request({
    url: '/system/indexSave',
    method: 'POST',
    data
  })
}

export function getIndexSystem() {
  return request({
    url: '/system/getindex',
    method: 'get'
  })
}
export function getLogin_background() {
  return request({
    url: '/common/getSystem',
    method: 'get'
  })
}
