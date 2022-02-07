<template>
  <div class="dashboard-container">
    <el-row :gutter="40" class="panel-group">
      <el-col :xs="8" :sm="8" :lg="4" class="card-panel-col">
        <div class="card-panel">
          <div class="card-panel-icon-wrapper icon-people">
            <svg-icon icon-class="peoples" class-name="card-panel-icon" />
          </div>
          <div class="card-panel-description">
            <div class="card-panel-text">
              总注册数
            </div>
            <span class="card-panel-num"> {{ equipment.register_count }}</span>
          </div>
        </div>
      </el-col>
      <el-col :xs="8" :sm="8" :lg="4" class="card-panel-col">
        <div class="card-panel">
          <router-link to="/operating/on">
            <div class="card-panel-icon-wrapper icon-message">
              <svg-icon icon-class="people" class-name="card-panel-icon" />
            </div>
          </router-link>
          <div class="card-panel-description">
            <div class="card-panel-text">
              当前在线人数
            </div>
            <span class="card-panel-num"> {{ equipment.online_count }}</span>
            <!--            <count-to :start-val="equipment.online_count" :end-val="equipment.online_count" class="card-panel-num" />-->
          </div>
        </div>
      </el-col>
      <el-col :xs="8" :sm="8" :lg="5" class="card-panel-col">
        <div class="card-panel">
          <div class="card-panel-icon-wrapper icon-money">
            <svg-icon icon-class="money" class-name="card-panel-icon" />
          </div>
          <div class="card-panel-description">
            <div class="card-panel-text">
              NFT交易额/BUSD
            </div>
            <span class="card-panel-num">{{ equipment.nft_trading }}</span>
          </div>
        </div>
      </el-col>
      <el-col :xs="8" :sm="8" :lg="5" class="card-panel-col">
        <div class="card-panel">
          <div class="card-panel-icon-wrapper icon-money">
            <svg-icon icon-class="money" class-name="card-panel-icon" />
          </div>
          <div class="card-panel-description">
            <div class="card-panel-text">
              Box交易额/BUSD
            </div>
            <span class="card-panel-num"> {{ equipment.box_trading }}</span>
          </div>
        </div>
      </el-col>
      <el-col :xs="8" :sm="8" :lg="5" class="card-panel-col">
        <div class="card-panel">
          <div class="card-panel-icon-wrapper icon-money">
            <svg-icon icon-class="money" class-name="card-panel-icon" />
          </div>
          <div class="card-panel-description">
            <div class="card-panel-text">
              Box销售额/BUSD
            </div>
            <span class="card-panel-num"> {{ equipment.box_sales }}</span>
          </div>
        </div>
      </el-col>
    </el-row>
    <div style="margin-left:20px;display: inline;margin-top: 100px">
      <el-card class="box-card" style="width: 100%;margin-top: 30px;float: left">
        <p style="text-align: center">用户留存数据</p>
        <div class="block">
          <el-date-picker
            v-model="date"
            type="daterange"
            value-format="yyyy-MM-dd"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
          />
          <el-button icon="el-icon-refresh" style="margin-left: 15px;margin-top:15px" size="mini" circle @click="retained" />
        </div>
        <el-table
          :data="RoomChange.list"
          style=""
        >
          <el-table-column
            prop="times"
            label="日期"
          />
          <el-table-column
            prop="daynews"
            label="新增人数"
          />
          <el-table-column
            prop="news"
            label="活跃人数"
          />
          <el-table-column
            prop="days1"
            label="次日留存率"
          />
          <el-table-column
            prop="days3"
            label="三日留存率"
          />
          <el-table-column
            prop="days7"
            label="七日留存率"
          />
          <el-table-column
            prop="nft_trading"
            label="NFT交易额/BUSD"
          />
          <el-table-column
            prop="box_trading"
            label="Box交易额/BUSD"
          />
          <el-table-column
            label="Box销售额/BUSD"
          >
            <template slot-scope="{row}">
              <span>{{ row.box_sales }}</span>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </div>
  </div>

</template>

<script>
import { mapGetters } from 'vuex'
import { getStatistical, getRetained } from '@/api/home'
// import CountTo from 'vue-count-to'
export default {
  name: 'Dashboard',
  components: {
    // CountTo
  },
  data() {
    return {
      hallList: null,
      merchants: 0,
      player: 0,
      spending: {},
      equipment: {
      },
      date: [],
      RoomChange: {
        list: ''
      }
    }
  },
  computed: {
    ...mapGetters([
      'name'
    ])
  },
  mounted() {
    this.get()
    this.retained()
  },
  created() {
  },
  methods: {
    get() {
      getStatistical().then(res => {
        this.equipment = res.data
      })
    },
    retained() {
      getRetained(this.date).then(res => {
        this.RoomChange.list = res.data.list
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.dashboard {
  &-container {
    margin: 30px;
  }
  &-text {
    font-size: 30px;
    line-height: 46px;
  }
  .text {
    font-size: 14px;
  }
}

.panel-group {
  margin-top: 18px;
  .card-panel-col {
    margin-bottom: 32px;
  }

  .card-panel {
    height: 108px;
    cursor: pointer;
    font-size: 12px;
    position: relative;
    overflow: hidden;
    color: #666;
    background: #fff;
    box-shadow: 4px 4px 40px rgba(0, 0, 0, .05);
    border-color: rgba(0, 0, 0, .05);

    &:hover {
      .card-panel-icon-wrapper {
        color: #fff;
      }

      .icon-people {
        background: #40c9c6;
      }

      .icon-message {
        background: #36a3f7;
      }

      .icon-money {
        background: #f4516c;
      }

      .icon-shopping {
        background: #34bfa3
      }
    }

    .icon-people {
      color: #40c9c6;
    }

    .icon-message {
      color: #36a3f7;
    }

    .icon-money {
      color: #f4516c;
    }

    .icon-shopping {
      color: #34bfa3
    }
    .card-panel-num {
      float: right;
    }
    .card-panel-icon-wrapper {
      float: left;
      margin: 14px 0 0 14px;
      padding: 16px;
      transition: all 0.38s ease-out;
      border-radius: 6px;
    }

    .card-panel-icon {
      float: left;
      font-size: 48px;
    }

    .card-panel-description {
      float: right;
      font-weight: bold;
      margin: 26px;
      margin-left: 0px;

      .card-panel-text {
        line-height: 18px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 16px;
        margin-bottom: 12px;
      }

      .card-panel-num {
        font-size: 20px;
      }
    }
  }
}

@media (max-width:550px) {
  .card-panel-description {
    display: none;
  }
  .el-col-xs-8 {
    width: 100%;
  }
  .el-date-editor--daterange{
    width: 268px;
  }
  .card-panel-description {
    display: block;
  }
  .card-panel-icon-wrapper {
    /*float: none !important;*/
    /*width: 100%;*/
    /*height: 100%;*/
    /*margin: 0 !important;*/

    .svg-icon {
      display: block;
      margin: 14px auto !important;
      float: none !important;
    }
  }
}
</style>
