<template>
  <div class="app-container">
    <el-collapse>
      <el-collapse-item style="margin-left: 20px" title="高级搜索" name="1">
        <el-form :inline="true" :model="listQuery" size="small" class="demo-form-inline">
          <el-form-item label="操作内容">
            <el-input v-model="listQuery.keyword" />
          </el-form-item>
          <el-form-item label="请求时间" style="margin-left: 20px">
            <el-date-picker
              v-model="listQuery.time"
              type="date"
              placeholder="选择日期"
            />
          </el-form-item>
          <el-form-item label="管理员" prop="email">
            <el-select v-model="listQuery.uid" placeholder="请选择">
              <el-option
                v-for="item in user"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="getList">查询</el-button>
          </el-form-item>
          <el-form-item>
            <el-button type="warning" @click="resetSearch">重置</el-button>
          </el-form-item>
        </el-form>
      </el-collapse-item>
    </el-collapse>
    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      size="small"
      style="width: 100%;margin-top: 20px"
      fit
      highlight-current-row
      :header-cell-style="tableHeaderCellStyle"
    >
      <el-table-column label="序号" align="center" width="100">
        <template slot-scope="row">
          <span>{{ listQuery.page===1?row.$index + 1:(listQuery.page -1)*listQuery.limit+(row.$index + 1) }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" style="cursor: pointer" @click="getInfo(row)">{{ row.name }} </span>
        </template>
      </el-table-column>
      <el-table-column label="主机" align="center">
        <template slot-scope="{row}">
          <span>{{ row.ip }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作系统" align="center">
        <template slot-scope="{row}">
          <span>{{ row.client }}</span>
        </template>
      </el-table-column>
      <el-table-column label="接口" align="center">
        <template slot-scope="{row}">
          <span>{{ row.api }}</span>
        </template>
      </el-table-column>
      <el-table-column label="管理员" align="center">
        <template slot-scope="{row}">
          <span>{{ row.user }}</span>
        </template>
      </el-table-column>
      <el-table-column label="请求时间" align="center">
        <template slot-scope="{row}">
          <span>{{ row.time }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center">
        <template slot-scope="{row}">
          <el-tag v-if="row.status == 0" type="success">成功</el-tag>
          <el-tag v-if="row.status == 1" type="danger">失败</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="success" size="mini" @click="getInfo(row)">
            查看
          </el-button>
          <el-button v-if=" roles == 1" type="danger" size="mini" @click="handleDelete(row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" style="float: right" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList()" />

    <el-dialog :visible.sync="dialogInfo">
      <el-descriptions title="详细信息">
        <el-descriptions-item label="请求api">{{ temp.api }}</el-descriptions-item>
        <el-descriptions-item label="操作">{{ temp.name }}</el-descriptions-item>
        <el-descriptions-item label="请求时间">{{ temp.time }}</el-descriptions-item>
        <el-descriptions-item label="请求IP">{{ temp.ip }}</el-descriptions-item>
        <el-descriptions-item label="操作人员">{{ temp.user }}</el-descriptions-item>
        <el-descriptions-item label="请求状态">
          <el-tag v-if="temp.status == 0" size="mini">成功</el-tag>
          <el-tag v-if="temp.status == 1" size="mini">失败</el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="请求参数">{{ temp.info }}</el-descriptions-item>
      </el-descriptions>
      <div style="text-align:right;">
        <el-button type="danger" @click="dialogInfo=false">关闭</el-button>
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
import { getList, del } from '@/api/log'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination
import waves from '../../directive/waves'
import { mapGetters } from 'vuex'
import { getUserAll } from '@/api/user'
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
        time: '',
        uid: undefined,
        keyword: ''
      },
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
      dialogInfo: false,
      user: {}
    }
  },
  computed: {
    ...mapGetters([
      'roles'
    ])
  },
  created() {
    this.getList()
    this.getUserList()
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
    getInfo(row) {
      this.temp = Object.assign({}, row) // copy obj
      this.dialogInfo = true
    },
    resetSearch() {
      this.listQuery.time = ''
      this.listQuery.keyword = ''
      this.listQuery.uid = undefined
      getList(this.listQuery).then(res => {
        this.list = res.data.list
        this.total = res.data.total
      })
    },
    handleDelete(id) {
      del({ id: id }).then(res => {
        if (res.code === 0) {
          this.$message({
            type: 'success',
            message: '操作成功'
          })
          this.getList()
        }
      })
    },
    getUserList() {
      getUserAll().then(res => {
        if (res.code === 0) {
          this.user = res.data.list
        }
      })
    },
    tableHeaderCellStyle() {
      return 'word-break: break-word;background-color: #f8f8f9;color: #515a6e;height: 40px;font-size: 13px'
    }
  }
}
</script>
