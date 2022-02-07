<template>
  <div class="app-container">
    <div>
      <el-row :gutter="20">
        <el-col :span="22" :xs="22" style="margin-left: 20px;">
          <el-card>
            <el-tabs v-model="activeTab">
              <el-tab-pane label="基础设置" name="admin">
                <Admin />
              </el-tab-pane>
            </el-tabs>
          </el-card>
        </el-col>

      </el-row>
    </div>
  </div>
</template>

<script>
import Admin from './components/Admin'
export default {
  // eslint-disable-next-line vue/no-unused-components
  components: { Admin },
  data() {
    return {
      activeTab: 'admin'
    }
  },
  computed: {
  },
  created() {
  },
  methods: {
  }
}
</script>
