<template>
  <div class="app-container">
    <el-input v-model="key" class="filter-item" placeholder="缓存key" style="width: 60%;" />
    <el-button class="filter-item" icon="el-icon-search" type="primary" @click="handleFilter">缓存key查询</el-button>
    <div style="margin: 20px 0;" />

    <el-input
      v-model="value"
      style="background-color: #5a5e66"
      type="textarea"
      disabled="true"
      :autosize="{ minRows: 20, maxRows: 20}"
    />
    <el-button class="filter-item" type="danger" style="margin-top: 20px;float: right" @click="handleDelete">清空缓存</el-button>
  </div>
</template>
<style>
  .customWidth{
    width:100%;
  }
</style>
<script>
import { getCache, deleteCache } from '@/api/cache'
export default {
  data() {
    return {
      key: '',
      value: ''
    }
  },
  created() {
    this.getList()
  },
  methods: {
    handleDelete() {
      this.$confirm('你确定清空吗?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '关闭',
        type: 'warning'
      })
        .then(async() => {
          await deleteCache()
          this.$message({
            type: 'success',
            message: '操作成功'
          })
        })
        .catch(err => {
          console.error(err)
        })
    },
    handleFilter() {
      this.key
      getCache({ key: this.key }).then(res => {
        if (res.code === 0) {
          this.value = res.data
        }
      })
    }
  }
}
</script>
