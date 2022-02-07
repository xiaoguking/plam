<template>
  <div class="app-container">
    <div class="filter-container">
      <label class="el-form-item__label" style="width: 68px;line-height: 30px;">菜单名称</label>
      <el-input v-model="listQuery.keyword" class="filter-item" size="small" placeholder="请输入菜单名称" style="width: 200px;margin-right: 20px" @keyup.enter.native="handleFilter" />
      <el-button class="filter-item" icon="el-icon-search" size="small" type="primary" @click="handleFilter">
        搜索
      </el-button>
      <el-button class="filter-item" icon="el-icon-refresh" size="small" @click="handleRefresh">
        重置
      </el-button>
      <el-button class="filter-item" icon="el-icon-add" size="small" style="margin-left: 10px;" type="success" @click="handleCreate">
        新增菜单
      </el-button>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      size="small"
      row-key="id"
      fit
      highlight-current-row
      style="width: 100%;margin-top: 20px"
      :header-cell-style="tableHeaderCellStyle"
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
    >
      <el-table-column label="菜单名称" align="center" width="200">
        <template slot-scope="{row}">
          <span class="link-type" style="cursor: pointer">{{ row.title }} </span>
        </template>
      </el-table-column>
      <el-table-column label="组件" align="center" width="200">
        <template slot-scope="{row}">
          <span>{{ row.component }}</span>
        </template>
      </el-table-column>
      <el-table-column label="路由" align="center" width="250">
        <template slot-scope="{row}">
          <span>{{ row.path }}</span>
        </template>
      </el-table-column>
      <el-table-column label="创建时间" align="center" width="250">
        <template slot-scope="{row}">
          <span>{{ row.create_time }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" class-name="small-padding fixed-width">
        <template slot-scope="{row}">

          <el-button icon="el-icon-edit" size="mini" circle @click="handleUpdate(row)" />
          <el-button icon="el-icon-circle-plus-outline" size="mini" circle @click="handleCreateChildren(row)" />
          <el-button size="mini" icon="el-icon-delete" circle @click="handleDelete(row)" />
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" width="700px" class="customWidth">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="100px" style="width: 70%;margin:auto">
        <el-form-item label="名称" prop="title">
          <el-input v-model="temp.title" placeholder="请输入菜单名称" />
        </el-form-item>
        <el-form-item label="图标" prop="icon">
          <el-input v-model="temp.icon" placeholder="请输入图标字符串" />
        </el-form-item>
        <el-form-item label="组件" prop="component">
          <el-input v-model="temp.component" placeholder="请输入路由组件标识符" />
        </el-form-item>
        <el-form-item label="路由" prop="path">
          <el-input v-model="temp.path" placeholder="请输入路由" />
        </el-form-item>
        <el-form-item label="重定向地址" prop="redirect">
          <el-input v-model="temp.redirect" placeholder="请输入重定向地址" />
        </el-form-item>
        <el-form-item label="排序" prop="order">
          <el-input v-model="temp.order" placeholder="请输入菜单序号" />
        </el-form-item>
        <div>
          <el-switch
            v-model="temp.alwaysShow"
            active-color="#13ce66"
            inactive-color="#ff4949"
            active-text="操作"
            inactive-text="路由"
            active-value="0"
            inactive-value="1"
          />
          <el-switch
            v-model="temp.hidden"
            style="margin-left: 20px"
            active-color="#ff4949"
            inactive-color="#13ce66"
            active-text="隐藏"
            inactive-text="显示"
            active-value="1"
            inactive-value="0"
          />
          <el-switch
            v-model="temp.status"
            style="margin-left: 20px"
            active-color="#ff4949"
            inactive-color="#13ce66"
            active-text="关闭"
            inactive-text="开启"
            active-value="1"
            inactive-value="0"
          />
        </div>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogFormVisible = false">
          关闭
        </el-button>
        <el-button size="small" type="primary" @click="dialogStatus==='create'?createData():updateData()">
          保存
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>
<style>
  .customWidth{
    width:100%;
  }
</style>
<script>
import { add, getList, edit, del } from '../../api/menus'
import waves from '../../directive/waves' // waves directive
var qs = require('querystring')
export default {
  directives: { waves },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        importance: undefined,
        keyword: undefined,
        pid: 0
      },
      importanceOptions: [1, 2, 3],
      sortOptions: [{ label: 'ID Ascending', key: '+id' }, { label: 'ID Descending', key: '-id' }],
      statusOptions: ['开启', '关闭'],
      showReviewer: false,
      temp: {
        alwaysShow: 0,
        hidden: 0,
        status: 0
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: '编辑',
        create: '添加'
      },
      dialogPvVisible: false,
      delData: {
        id: ''
      },
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }]
      },
      downloadLoading: false
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.listLoading = true
      getList(this.listQuery).then(response => {
        this.list = response.data.list
        this.total = response.data.total
        // Just to simulate the time of the request
        setTimeout(() => {
          this.listLoading = false
        }, 1.5 * 10)
      })
    },
    getListchrid(pid) {
      this.listQuery.pid = pid
      this.getList()
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row) // copy obj
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    resetTemp() {
      this.temp = {
      }
    },
    handleCreate() {
      this.resetTemp()
      this.temp.component = 'Layout'
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    handleCreateChildren(row) {
      this.resetTemp()
      this.temp.pid = row.id
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    createData() {
      add(qs.stringify(this.temp)).then(() => {
        this.dialogFormVisible = false
        this.$message({
          type: 'success',
          message: '操作成功'
        })
        this.getList()
      })
    },
    updateData() {
      const tempData = Object.assign({}, this.temp)
      edit(qs.stringify(tempData)).then(() => {
        this.dialogFormVisible = false
        this.$message({
          type: 'success',
          message: '操作成功'
        })
        this.getList()
      })
    },
    handleDelete(row) {
      this.$confirm('你确定删除这个菜单吗?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '关闭',
        type: 'warning'
      })
        .then(async() => {
          this.delData._id = row.id
          await del(this.delData)
          this.$message({
            type: 'success',
            message: '操作成功'
          })
          this.getList()
        })
        .catch(err => { console.error(err) })
    },
    handleFilter() {
      this.listQuery.page = 1
      this.listQuery.pid = 0
      this.getList()
    },
    handleRefresh() {
      this.listQuery.keyword = ''
      this.listQuery.page = 1
      this.listQuery.pid = 0
      this.getList()
    },
    tableHeaderCellStyle() {
      return 'word-break: break-word;background-color: #f8f8f9;color: #515a6e;height: 40px;font-size: 13px'
    }
  }
}
</script>
<style lang="scss" scoped>
    .el-table__header {
      th {
        word-break: break-word;
        background-color: #f8f8f9;
        color: #515a6e;
        height: 40px;
        font-size: 13px
      }
    }
</style>
