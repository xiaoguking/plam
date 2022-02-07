<template>
  <el-form label-width="100px" class="demo-ruleForm" size="small">
    <el-form-item label="昵称">
      <el-input v-model.trim="user.name" />
    </el-form-item>
    <el-form-item label="手机号码">
      <el-input v-model.trim="user.phone" />
    </el-form-item>
    <el-form-item label="邮箱">
      <el-input v-model.trim="user.email" />
    </el-form-item>
    <el-form-item>
      <el-button type="primary" size="small" @click="submit">更新</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { update } from '../../../api/user'
var qs = require('querystring')
export default {
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
          phone: ''
        }
      }
    }
  },
  methods: {
    submit() {
      update(qs.stringify(this.user)).then(res => {
        if (res.code === 0) {
          this.$message({
            message: '操作成功',
            type: 'success',
            duration: 5 * 1000
          })
        }
      })
    }
  }
}
</script>
