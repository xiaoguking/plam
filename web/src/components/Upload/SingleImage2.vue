<template>
  <el-upload
    action="http://api.xiaogu.com/api/uploadImg/upload"
    list-type="picture-card"
    :on-success="handleAvatarSuccess"
  >
    <i class="el-icon-plus" />
    <el-dialog :visible.sync="dialogVisible">
      <img width="100%" :src="dialogImageUrl" alt="">
    </el-dialog>
  </el-upload>

</template>

<script>
export default {
  data() {
    return {
      dialogImageUrl: '',
      dialogVisible: false
    }
  },
  methods: {
    handleAvatarSuccess(res) {
      this.dialogImageUrl = res.data.img
    }
  }
}
</script>
