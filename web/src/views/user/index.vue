<template>
  <div class="app-container">
    <div class="filter-container">
      <label class="el-form-item__label" style="width: 100px;line-height: 30px;">管理员昵称</label>
      <el-input v-model="listQuery.keyword" class="filter-item" placeholder="请输入管理员昵称" style="width: 200px;margin-right: 15px" size="small" @keyup.enter.native="handleFilter" />
      <el-button class="filter-item" icon="el-icon-search" size="small" type="primary" @click="handleFilter">
        搜索
      </el-button>
      <el-button class="filter-item" icon="el-icon-add" style="margin-left: 10px;" size="small" type="success" @click="handleCreate">
        添加管理员
      </el-button>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      size="small"
      fit
      highlight-current-row
      :header-cell-style="tableHeaderCellStyle"
      style="width: 100%;margin-top: 20px"
    >
      <el-table-column align="center" label="序号" width="100">
        <template slot-scope="row">
          <span>{{ listQuery.page===1?row.$index + 1:(listQuery.page*listQuery.limit)+(row.$index + 1) }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="账号">
        <template slot-scope="{row}">
          <span class="link-type">{{ row.username }} </span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="昵称">
        <template slot-scope="{row}">
          <span>{{ row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="邮箱">
        <template slot-scope="{row}">
          <span>{{ row.email }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="电话">
        <template slot-scope="{row}">
          <span>{{ row.phone }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="角色">
        <template slot-scope="{row}">
          <span>{{ row.roles_name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="创建时间">
        <template slot-scope="{row}">
          <span>{{ row.create_time }}</span>
        </template>
      </el-table-column>
      <!--      <el-table-column label="状态" class-name="status-col" width="100">-->
      <!--        <template slot-scope="{row}">-->
      <!--          <el-tag :type="row.status | statusFilter">-->
      <!--            {{ row.status }}-->
      <!--          </el-tag>-->
      <!--        </template>-->
      <!--      </el-table-column>-->
      <el-table-column align="center" class-name="small-padding fixed-width" label="操作">
        <template slot-scope="{row}">
          <el-button v-if="row.username != 'admin' && roles == 1" size="mini" @click="handleUpdate(row)">
            编辑
          </el-button>
          <el-button v-if="row.username != 'admin'" size="mini" type="danger" @click="handleDelete(row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" style="float: right" :limit.sync="listQuery.limit" :page.sync="listQuery.page" :total="total" @pagination="getList()" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" width="700px" class="customWidth">
      <el-form ref="dataForm" :model="temp" :rules="rules" label-position="left" label-width="100px" style="width:70%;margin:auto">
        <el-form-item label="账号" prop="username">
          <el-input v-if="dialogStatus==='update'" v-model="temp.username" disabled />
          <el-input v-if="dialogStatus==='create'" v-model="temp.username" placeholder="请输入管理员账号" />
        </el-form-item>
        <el-form-item label="昵称" prop="name">
          <el-input v-model="temp.name" placeholder="请输入管理员昵称" />
        </el-form-item>
        <el-form-item label="联系电话" prop="phone">
          <el-input v-model="temp.phone" placeholder="请输入管理员手机号" />
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="temp.email" placeholder="请输入管理员邮箱" />
        </el-form-item>
        <el-form-item label="角色" prop="email">
          <el-select v-model="temp.roles_id" placeholder="请选择管理员角色">
            <el-option
              v-for="item in rolesAll"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </el-form-item>
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
  .customWidth {
    width: 100%;
  }
</style>
<script>
import { add, getList, del } from '../../api/user'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination
import waves from '../../directive/waves'
import { getRolesList } from '../../api/roles'
import { mapGetters } from 'vuex' // waves directive
var qs = require('querystring')
export default {
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        keyword: undefined,
        sort: '+id'
      },
      gender: [{
        value: 'f',
        label: '男'
      }, {
        value: 'm',
        label: '女'
      }],
      rolesAll: [],
      importanceOptions: [1, 2, 3],
      sortOptions: [{ label: 'ID Ascending', key: '+id' }, { label: 'ID Descending', key: '-id' }],
      statusOptions: ['开启', '关闭'],
      showReviewer: false,
      temp: {
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
      downloadLoading: false,
      dialogFormVisibleEditUser: false
    }
  },
  computed: {
    ...mapGetters([
      'roles'
    ])
  },
  created() {
    this.getList()
    this.getRolesList()
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
    getRolesList() {
      getRolesList().then(response => {
        this.rolesAll = response.data
      })
    },
    handleCreate() {
      this.temp = {}
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    handleUpdate(row) {
      this.temp = row
      this.dialogStatus = 'update'
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
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          add(qs.stringify(this.temp)).then(() => {
            this.dialogFormVisible = false
            this.$message({
              type: 'success',
              message: '操作成功'
            })
            this.getList()
          })
        }
      })
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          add(qs.stringify(this.temp)).then(() => {
            this.dialogFormVisible = false
            this.$message({
              type: 'success',
              message: '操作成功'
            })
            this.getList()
          })
        }
      })
    },
    handleDelete(row) {
      this.$confirm('你确定删除吗?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '关闭',
        type: 'warning'
      })
        .then(async() => {
          this.delData.id = row.id
          await del(this.delData)
          this.$message({
            type: 'success',
            message: '操作成功'
          })
          this.getList()
        })
        .catch(err => {
          console.error(err)
        })
    },
    handleFilter() {
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
