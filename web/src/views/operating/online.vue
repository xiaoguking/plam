<template>
  <div class="dashboard-container">
    <div style="width: 100%">
      <div style="background-color: #409EFF;width: 300px;text-align: center;margin: auto"><p style="padding: 10px;">用户24小时在线数据</p></div>
    </div>
    <div id="myChart" :style="{width: '100%', height: '500px'}" />
  </div>
</template>

<script>
import * as echarts from 'echarts'
import { getOnlineIng } from '@/api/home'

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
          data: ['昨天', '今天']
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
          name: '在线人数'
        },
        series: [
          {
            name: '昨天',
            type: 'line',
            data: [],
            symbolSize: 1.5 // 设定实心点的大小
            // itemStyle: {
            //   normal: {
            //     // color: '#6cb041',
            //     lineStyle: {
            //       width: 1.5 // 设置线条粗细
            //     }
            //   }
            // }
          },
          {
            name: '今天',
            type: 'line',
            data: [],
            symbolSize: 1.5 // 设定实心点的大小
            // symbol: 'circle', // 设定为实心点
            // symbolSize: 1, // 设定实心点的大小
            // itemStyle: {
            //   normal: {
            //     // color: '#409EFF',
            //     lineStyle: {
            //       width: 1.5 // 设置线条粗细
            //     }
            //   }
            // }
          }
        ]
      }
      // 异步加载数据
      getOnlineIng().then(res => {
        // 填入数据
        myChart.setOption({
          xAxis: {
            data: res.data['time']
          },
          series: [
            {
              name: '今天',
              data: res.data['today']
            },
            {
              name: '昨天',
              data: res.data['yesterday']
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
