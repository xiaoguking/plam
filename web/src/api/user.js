import request from '@/utils/request'
export function login(data) {
  return request({
    url: '/user/login',
    method: 'post',
    data
  })
}

export function getInfo(data) {
  return request({
    url: '/user/info',
    method: 'post',
    data
  })
}

export function logout() {
  return request({
    url: '/user/logout',
    method: 'get'
  })
}

export function getList(params) {
  return request({
    url: '/user/List',
    method: 'get',
    params
  })
}

export function add(data) {
  return request({
    url: '/user/add',
    method: 'post',
    data
  })
}

export function del(params) {
  return request({
    url: '/user/delete',
    method: 'get',
    params
  })
}

export function updatePassword(params) {
  return request({
    url: '/user/updatePwd',
    method: 'get',
    params
  })
}

export function accLog() {
  return request({
    url: '/user/accLog',
    method: 'get'
  })
}

export function update(data) {
  return request({
    url: '/user/updateInfo',
    method: 'post',
    data
  })
}

export function getUserAll(params) {
  return request({
    url: '/user/getUserAll',
    method: 'get',
    params
  })
}
