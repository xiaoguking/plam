import request from '@/utils/request'
export function getList(params) {
  return request({
    url: '/cron/list',
    method: 'get',
    params
  })
}

export function save(data) {
  return request({
    url: '/cron/save',
    method: 'post',
    data
  })
}

export function del(data) {
  return request({
    url: '/cron/delete',
    method: 'post',
    data
  })
}

export function updateStatus(data) {
  return request({
    url: '/cron/updateStatus',
    method: 'post',
    data
  })
}

export function getLog(params) {
  return request({
    url: '/cron/getCronLog',
    method: 'get',
    params
  })
}

export function execCron(params) {
  return request({
    url: '/cron/execCron',
    method: 'get',
    params
  })
}
