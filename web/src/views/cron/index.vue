<template>
  <div class="app-container">
    <div class="filter-container">
      <label class="el-form-item__label" style="width: 68px;line-height: 30px;">任务名称</label>
      <el-input v-model="listQuery.name" class="filter-item" size="small" placeholder="请输入任务名称" style="width: 200px;margin-right: 15px" @keyup.enter.native="handleFilter" />
      <el-button class="filter-item" icon="el-icon-search" size="small" type="primary" @click="handleFilter">
        搜索
      </el-button>
      <el-button class="filter-item" icon="el-icon-refresh" size="small" @click="handleRefresh">
        重置
      </el-button>
      <el-button class="filter-item" icon="el-icon-add" size="small" style="margin-left: 10px;" type="success" @click="handleCreate">
        添加任务
      </el-button>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      size="small"
      fit
      style="width: 100%;margin-top: 20px"
      :header-cell-style="tableHeaderCellStyle"
    >
      <el-table-column align="center" label="序号" prop="id" width="50">
        <template slot-scope="row">
          <span>{{ listQuery.page===1?row.$index + 1:(listQuery.page*listQuery.limit)+(row.$index + 1) }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="名称">
        <template slot-scope="{row}">
          <span class="link-type">{{ row.name }} </span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="任务规则">
        <template slot-scope="{row}">
          <span>{{ row.cron }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="类名">
        <template slot-scope="{row}">
          <span>{{ row.class }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="方法名">
        <template slot-scope="{row}">
          <span>{{ row.function }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="上次执行时间">
        <template slot-scope="{row}">
          <span>{{ row.next_time }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="状态">
        <template slot-scope="{row}">
          <el-tag v-if="row.status == 0">开启</el-tag>
          <el-tag v-if="row.status == 1">关闭</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" min-width="200px">
        <template slot-scope="{row}">
          <el-button size="small" type="primary" @click="handleUpdate(row)">编辑</el-button>
          <el-button v-if="row.status != 0" size="mini" @click="handleModifyStatus(row,0)">
            开启
          </el-button>
          <el-button v-if="row.status == 0" size="mini" @click="handleModifyStatus(row,1)">
            关闭
          </el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row)">删除</el-button>
          <el-button size="mini" type="success" @click="execCron(row.id)">立即执行</el-button>
          <el-button size="mini" type="info" @click="getLog(row.id)">查看日志</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :limit.sync="listQuery.limit" style="float: right" :page.sync="listQuery.page" :total="total" @pagination="getList()" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" width="700px" class="customWidth">
      <el-form ref="dataForm" :model="temp" label-position="left" label-width="80px" style="margin: 20px 20px">
        <el-form-item label="任务名称">
          <el-input v-model="temp.name" placeholder="请输入任务名称" />
        </el-form-item>
        <el-form-item label="任务类">
          <el-input v-model="temp.class" placeholder="请输入任务所在的类名称 区分大小写" />
        </el-form-item>
        <el-form-item label="任务方法">
          <el-input v-model="temp.function" placeholder="请输入任务所在的方法名 区分大小写" />
        </el-form-item>
        <el-form-item label="任务规则">
          <el-input v-model="temp.cron" placeholder="请输入任务规则 */5 * * * * *: 每隔5秒执行一次 " />
        </el-form-item>
        <el-form-item label="开始时间">
          <el-date-picker
            v-model="temp.begin_time"
            type="datetime"
            placeholder="选择日期时间"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogFormVisible = false">
          关闭
        </el-button>
        <el-button size="small" type="primary" @click="createData()">
          保存
        </el-button>
      </div>
    </el-dialog>

    <el-dialog :title="'执行日志'" :visible.sync="cronLogForm" class="customWidth" top="80px">
      <el-table
        :key="tableKey"
        :data="log.list"
        size="mini"
        border="true"
        fit
        style="width: 100%;margin-top: 20px"
      >
        <el-table-column align="center" label="执行时间">
          <template slot-scope="{row}">
            <span>{{ row.time }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="执行时长(S)">
          <template slot-scope="{row}">
            <span>{{ row.time_len }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="执行信息">
          <template slot-scope="{row}">
            <el-button type="text" @click="open(row.msg)">点击查看</el-button>
          </template>
        </el-table-column>
        <el-table-column align="center" label="状态">
          <template slot-scope="{row}">
            <el-tag v-if="row.code == 0">成功</el-tag>
            <el-tag v-if="row.code == 1">失败</el-tag>
          </template>
        </el-table-column>
      </el-table>
      <pagination v-show="log.total>0" :limit.sync="log.listQuery.limit" :page.sync="log.listQuery.page" :total="log.total" @pagination="getLogList()" />
    </el-dialog>

    <el-dialog
      title="日志详情"
      :visible.sync="dialogVisibleInfo"
      width="30%"
      :data="logInfo"
    >
      <span>{{ logInfo }}</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisibleInfo = false">关闭</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<style>
  .customWidth {
    width: 100%;
  }
</style>
<script>
import { save, getList, del, updateStatus, getLog, execCron } from '../../api/cron'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination
import waves from '../../directive/waves'
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
        limit: 10,
        name: undefined
      },
      showReviewer: false,
      temp: {
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: '编辑',
        create: '添加'
      },
      cronLogForm: false,
      dialogPvVisible: false,
      delData: {
        id: ''
      },
      log: {
        list: null,
        total: 0,
        listLoading: true,
        listQuery: {
          page: 1,
          limit: 10,
          id: undefined
        }
      },
      dialogVisibleInfo: false,
      logInfo: ''
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
        setTimeout(() => {
          this.listLoading = false
        }, 1.5 * 10)
      })
    },
    handleUpdate(row) {
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
      this.temp = {}
      this.temp = row
    },
    handleCreate() {
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.temp = {}
    },
    handleCreateChildren() {
      this.dialogFormVisible = true
    },
    createData() {
      save(qs.stringify(this.temp)).then(() => {
        this.dialogFormVisible = false
        this.$message({
          type: 'success',
          message: '操作成功'
        })
        this.getList()
      })
    },
    handleDelete(row) {
      this.$confirm('你确定删除吗?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '关闭',
        type: 'warning'
      })
        .then(async() => {
          await del(qs.stringify({ id: row.id }))
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
    handleModifyStatus(row, status) {
      updateStatus(qs.stringify({ id: row.id, status: status })).then(res => {
        if (res.code === 0) {
          this.$message({
            type: 'success',
            message: '操作成功'
          })
          this.getList()
        }
      })
    },
    getLog(id) {
      this.cronLogForm = true
      this.log.listQuery.id = id
      this.getLogList()
    },
    getLogList() {
      getLog(this.log.listQuery).then(res => {
        this.log.list = res.data.list
        this.log.total = res.data.total
      })
    },
    open(msg) {
      this.logInfo = msg
      this.dialogVisibleInfo = true
    },
    execCron(id) {
      execCron({ id: id }).then(res => {
        if (res.code === 0) {
          this.$message({
            type: 'success',
            message: res.data
          })
        } else {
          this.$message({
            type: 'error',
            message: res.data.message
          })
        }
      })
    },
    handleRefresh() {
      this.listQuery.name = ''
      this.getList()
    },
    tableHeaderCellStyle() {
      return 'word-break: break-word;background-color: #f8f8f9;color: #515a6e;height: 40px;font-size: 13px'
    }
  }
}
</script>
