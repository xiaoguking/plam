<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
// import { getToken } from '@/utils/auth'
// import websocek from '@/websocek'

export default {
  name: 'App'
  // created() {
  //   // 当在任一路由页面被刷新时,即是根组件app被重新创建,此时可以进行webSocket重连
  //   let token
  //   // eslint-disable-next-line prefer-const
  //   token = getToken()
  //   if (token) {
  //     websocek.ws = new WebSocket(websocek.path)
  //     websocek.ws.binaryType = 'arraybuffer'
  //     if (websocek.ws.readyState === 1) {
  //       websocek.ws.send(JSON.stringify({ webLoginKey: 'L6Kjl%kBz.lhq)eqj*w3er^zl2nv*nal_s6k3#f' }))
  //     }
  //   }
  // }
}
</script>

<style lang="scss" scoped>
  .el-pagination {
    float:right;
  }
</style>
