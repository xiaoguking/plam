<template>
  <div class="dashboard-container">
    <div style="width: 100%">
      <div style="background-color: #409EFF;width: 300px;text-align: center;margin: auto"><p style="padding: 10px;">近七天营业额分析统计</p></div>
    </div>
    <div id="myChart" :style="{width: '100%', height: '500px'}" />
  </div>
</template>

<script>
import * as echarts from 'echarts'
import { getTurnover } from '@/api/home'

export default {
  data() {
    return {
      timeList: null
    }
  },
  mounted() {
    this.inits()
  },
  created() {
  },
  methods: {
    inits() {
      var chartDom = document.getElementById('myChart')
      var myChart = echarts.init(chartDom)
      var option
      option = {
        title: {
          text: ''
        },
        tooltip: {
          trigger: 'axis'
        },
        legend: {
          data: ['NFT交易额/BUSD', 'BOX交易额/BUSD', 'BOX销售额/BUSD']
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '3%',
          containLabel: true
        },
        toolbox: {
          feature: {
            saveAsImage: {}
          }
        },
        xAxis: {
          type: 'category',
          boundaryGap: false,
          data: []
        },
        yAxis: {
          type: 'value',
          name: '营业额(BUSD)'
        },
        series: [
          {
            name: 'NFT交易额/BUSD',
            type: 'line',
            data: []
          },
          {
            name: 'BOX交易额/BUSD',
            type: 'line',
            data: []
          },
          {
            name: 'BOX销售额/BUSD',
            type: 'line',
            data: []
          }
        ]
      }
      // 异步加载数据
      getTurnover().then(res => {
        // 填入数据
        myChart.setOption({
          xAxis: {
            data: res.data['x']
          },
          series: [
            {
              name: 'NFT交易额/BUSD',
              data: res.data['y']['nft_trading']
            },
            {
              name: 'BOX交易额/BUSD',
              data: res.data['y']['box_trading']
            },
            {
              name: 'BOX销售额/BUSD',
              data: res.data['y']['box_sales']
            }
          ]
        })
      })
      myChart.setOption(option)
    }
  }
}
</script>

<style scoped>

</style>
