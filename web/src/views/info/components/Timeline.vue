<template>
  <div class="block">
    <el-timeline>
      <el-timeline-item v-for="(item,index) of timeline" :key="index" :timestamp="item.time" placement="top">
        <el-card>
          <h4>{{ item.title }}</h4>
          <p>{{ item.content }}</p>
        </el-card>
      </el-timeline-item>
    </el-timeline>
  </div>
</template>

<script>
import { accLog } from '../../../api/user'

export default {
  data() {
    return {
      timeline: []
    }
  },
  created() {
    this.getUserLog()
  },
  methods: {
    async getUserLog() {
      const res = await accLog()
      console.log(res.data)
      this.timeline = res.data
    }
  }
}
</script>
