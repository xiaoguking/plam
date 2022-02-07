import request from '@/utils/request'

export function Notice() {
  return request({
    url: '/message/notice',
    method: 'get'
  })
}
