<template>
  <el-form style="margin-left: 20px">
    <el-form-item label="标题" style="margin-bottom: 20px;width: 70%;margin-top: 20px">
      <el-input v-model="postForm.title" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item label="关键字" style="margin-bottom: 20px;width: 70%">
      <el-input v-model="postForm.keyword" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item label="描述" style="margin-bottom: 20px;width: 70%">
      <el-input v-model="postForm.desc" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item style="margin-bottom: 20px;width: 70%" label="Ico图标:  ">
      <el-input v-model="postForm.ico" style="width: 60%;margin-left: 20px" placeholder="" disabled="true" />
      <el-upload
        style="display: contents;"
        class="upload-demo"
        action="http://api.xiaoguyun.cn/api/common/uploadImage"
        :show-file-list="false"
        :on-success="handleIcoSuccess"
      >
        <el-button size="medium " type="primary" style="height: 40px">点击上传</el-button>
      </el-upload>
      <div class="demo-image__placeholder" style="margin-left: 100px">
        <div class="block">
          <el-image :src="postForm.ico" :preview-src-list="postForm.ico" style="width: 200px;">
            <div slot="error" class="image-slot" />
          </el-image>
        </div>
      </div>
    </el-form-item>
    <el-form-item label="备案信息" style="margin-bottom: 20px;width: 70%">
      <el-input v-model="postForm.record" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item label="版权信息" style="margin-bottom: 20px;width: 70%">
      <el-input v-model="postForm.copyright" style="width: 60%;margin-left: 20px" />
    </el-form-item>
    <el-form-item label="网站状态" style="margin-bottom: 20px;width: 70%">
      <el-radio-group v-model="postForm.status" size="small" style="margin-left: 20px">
        <el-radio-button label="0">正常</el-radio-button>
        <el-radio-button label="1">升级维护</el-radio-button>
        <el-radio-button label="2">关闭</el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="统计代码" style="margin-bottom: 20px;width: 70%">
      <el-input
        v-model="postForm.statistical"
        type="textarea"
        :rows="4"
        style="width: 60%;margin-left: 20px"
        placeholder="请输入内容"
      />
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="submit">更新</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { IndexSave, getIndexSystem } from '../../../api/system'
var qs = require('querystring')
export default {
  data() {
    return {
      postForm: {
        keyword: '',
        title: '',
        ico: '',
        status: 0,
        desc: '',
        copyright: '',
        record: '',
        statistical: ''
      }
    }
  },
  created() {
    this.get()
  },
  methods: {
    submit() {
      IndexSave(qs.stringify(this.postForm)).then(res => {
        if (res.code === 0) {
          this.$message({
            message: '操作成功',
            type: 'success',
            duration: 5 * 1000
          })
        }
      })
    },
    handleIcoSuccess(res) {
      this.postForm.ico = res.data.img
    },
    get() {
      getIndexSystem().then(res => {
        this.postForm = res.data
      })
    }
  }
}
</script>
