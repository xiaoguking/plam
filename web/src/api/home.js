import request from '@/utils/request'

export function getStatistical() {
  return request({
    url: '/home/statistical',
    method: 'get'
  })
}

export function getRetained(params) {
  return request({
    url: '/home/getretained',
    method: 'get',
    params
  })
}

export function getOnlineIng() {
  return request({
    url: '/home/onlineIng',
    method: 'get'
  })
}

export function getTurnover() {
  return request({
    url: '/home/turnover',
    method: 'get'
  })
}
