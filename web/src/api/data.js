import request from '@/utils/request'

export function getVipList(params) {
  return request({
    url: '/vipData/list',
    method: 'get',
    params
  })
}

export function getPlayerList(params) {
  return request({
    url: '/PlayerData/list',
    method: 'get',
    params
  })
}

export function update(data) {
  return request({
    url: '/vipData/update',
    method: 'post',
    data
  })
}

export function updateStatus(params) {
  return request({
    url: '/vipData/updateStatus',
    method: 'get',
    params
  })
}
