<template>
  <div class="user-activity">
    <div v-for="(item,index) of message" :key="index" class="post" :timestamp="item.time" placement="top" style="margin-left: 20px">
      <div class="user-block" style="margin-bottom: 10px">
        {{ item.title }}
      </div>
      <div style="color: #005980;text-decoration: none;cursor: pointer;" v-html="item.content" />
    </div>
  </div>
</template>

<script>

export default {
  data() {
    return {
      message: []
    }
  },
  created() {
    this.get()
  },
  methods: {
    get() {
      var storage = window.localStorage
      var str = storage.getItem('message')
      this.message = JSON.parse(str)
    }
  }
}
</script>

<style lang="scss" scoped>
.user-activity {
  .user-block {

    .username,
    .description {
      display: block;
      margin-left: 50px;
      padding: 2px 0;
    }

    .username{
      font-size: 16px;
      color: #000;
    }

    :after {
      clear: both;
    }

    .img-circle {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      float: left;
    }

    span {
      font-weight: 500;
      font-size: 12px;
    }
  }

  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;

    .image {
      width: 100%;
      height: 100%;

    }

    .user-images {
      padding-top: 20px;
    }
  }

  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;

    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }

    .link-black {

      &:hover,
      &:focus {
        color: #999;
      }
    }
  }

}

.box-center {
  margin: 0 auto;
  display: table;
}

.text-muted {
  color: #777;
}
</style>
