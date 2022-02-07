import request from '@/utils/request'

export function getRoutes() {
  return request({
    url: '/roles/getRouter',
    method: 'get'
  })
}
export function getTerrRoutes() {
  return request({
    url: '/roles/getTerrRouters',
    method: 'get'
  })
}
export function getRoles() {
  return request({
    url: '/roles/list',
    method: 'get'
  })
}

export function addRole(data) {
  return request({
    url: '/roles/add',
    method: 'post',
    data
  })
}

export function updateRole(data) {
  return request({
    url: '/roles/update',
    method: 'post',
    data
  })
}

export function deleteRole(params) {
  return request({
    url: '/roles/delete',
    method: 'get',
    params
  })
}

export function getRolesList() {
  return request({
    url: '/roles/rolesList',
    method: 'get'
  })
}
