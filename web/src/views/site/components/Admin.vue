<template>
  <el-form style="margin-left: 20px">
    <el-form-item label="标题" style="margin-bottom: 20px;width: 70%;margin-top: 20px">
      <el-input v-model="postForm.title" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item style="margin-bottom: 20px;width: 70%" label="登录页背景图:">
      <el-input v-model="postForm.login_background" style="width: 60%;margin-left: 20px" placeholder="" disabled="true" />
      <el-upload
        style="display: contents;"
        class="upload-demo"
        :action="uploadUrl"
        :show-file-list="false"
        :on-success="handleLoginBackgroundSuccess"
      >
        <el-button size="medium " type="primary" style="height: 40px">点击上传</el-button>
      </el-upload>
      <div class="demo-image__placeholder" style="margin-left: 120px">
        <div class="block">
          <el-image :src="postForm.login_background" :preview-src-list="postForm.login_background" style="width: 200px;">
            <div slot="error" class="image-slot" />
          </el-image>
        </div>
      </div>
    </el-form-item>
    <!--    <el-form-item style="margin-bottom: 20px;width: 70%" label="Logo:  ">-->
    <!--      <el-input v-model="postForm.logo" style="width: 60%;margin-left: 20px" placeholder="" disabled="true" />-->
    <!--      <el-upload-->
    <!--        style="display: contents;"-->
    <!--        class="upload-demo"-->
    <!--        :action="uploadUrl"-->
    <!--        :show-file-list="false"-->
    <!--        :on-success="handleLoginLogoSuccess"-->
    <!--      >-->
    <!--        <el-button size="medium " type="primary" style="height: 40px">点击上传</el-button>-->
    <!--      </el-upload>-->
    <!--      <div class="demo-image__placeholder" style="margin-left: 100px">-->
    <!--        <div class="block">-->
    <!--          <el-image :src="postForm.logo" :preview-src-list="postForm.logo" style="width: 200px;">-->
    <!--            <div slot="error" class="image-slot" />-->
    <!--          </el-image>-->
    <!--        </div>-->
    <!--      </div>-->
    <!--    </el-form-item>-->
    <el-form-item label="新管理员默认密码" style="margin-bottom: 20px;width: 70%">
      <el-input v-model="postForm.password" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item label="管理员登录有效期(s)" style="margin-bottom: 20px;width: 70%">
      <el-input v-model="postForm.login_ex_time" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="submit">更新</el-button>
    </el-form-item>
  </el-form>
</template>
<script>
import { adminSave, getAdminSystem } from '../../../api/system'
var qs = require('querystring')
export default {
  data() {
    return {
      uploadUrl: process.env.VUE_APP_BASE_API + '/common/uploadImage',
      postForm: {
        login_background: '',
        logo: '',
        title: '',
        password: '',
        login_ex_time: ''
      }
    }
  },
  created() {
    this.get()
  },
  methods: {
    submit() {
      adminSave(qs.stringify(this.postForm)).then(res => {
        if (res.code === 0) {
          this.$message({
            message: '操作成功',
            type: 'success',
            duration: 5 * 1000
          })
        }
      })
    },
    handleLoginBackgroundSuccess(res) {
      this.postForm.login_background = res.data.img
    },
    handleLoginLogoSuccess(res) {
      this.postForm.logo = res.data.img
    },
    get() {
      getAdminSystem().then(res => {
        this.postForm = res.data
      })
    }
  }
}
</script>
