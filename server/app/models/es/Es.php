<?php

use Elasticsearch\ClientBuilder;

class Es
{
    public function __construct()
    {
        $this->config = json_decode(json_encode($GLOBALS['config']['es']),true);
        $host = $this->config['host'];
        $this->client = ClientBuilder::create()->setHosts($host)->build();
    }

    /**
     *创建索引
     */
    public function index($params){
        $index = $params['index'] ? $params['index'] : $this->config['index'];
        $type = $params['type'] ? $params['type'] : 'content';
        $param = [
            'index' => $index,
            'type' => $type,
            'body'=>array()
        ];

        $response =$this->client->index($param);
        return $response;
    }
    /**
     * 添加文档
     */
    public function add(array $params){
        $index = $params['index'] ? $params['index'] : $this->config['index'];
        $type = $params['type'] ? $params['type'] : 'content';

        $param = [
            'index' => $index,
            'type' => $type,
            'id' => $params['id'],
            'body'=>$params['body']  //['id' => 1,'title' => '中国','content' => 'assddffgghjhj','img' => 'aaa.png'] 形如这种的一维数组
        ];

        $response =$this->client->index($param);
        return $response;
    }
    /**
     * 搜索
     * array params
     * index 文档索引
     * doc 文档类型
     * keyword 搜索关键词
     * type 搜索类型 1标题搜索 2正文搜索
     * order 排序 1文档相关度 2时间排序 desc
     * page 页码
     * pcount 页码数
     *
     */
    function search(array  $params){
        $index = $params['index'] ? $params['index'] : $this->config['index'];
        $doc = $params['doc'] ? $params['doc'] : 'content';
        $page = $params['page'] >1 ?$params['page']:1;
        $offset = ($page-1) * $params['pcount'];
        $param = [
            'index' => $index,
            'type' => $doc,
            'size' => $params['pcount'],
            'from' => $offset,
            'body' => [
                'highlight' => [  //高亮显示
                'pre_tags' => ['<font color=\'red\'>'],  //highlight 的开始标签
                'post_tags' => ['</font>'],        //highlight 的结束标签
                'number_of_fragments' => 3,   //fragment 是指一段连续的文字。返回结果最多可以包含几段不连续的文字。默认是5。
                'fragment_size' => 200,    //一段 fragment 包含多少个字符。默认100。
                'fields' =>[
                    'title' => new \stdClass(),//这里必须给一个空对象
                    'content' => new \stdClass(),
                ]
                ],
            ],

        ];
        if($params['keyword'] !==''){
            if ($params['type'] == 1){
                //按标题搜索
                $param['body']['query']['match']['title'] = $params['keyword'];
            }else{
                //按正文搜索
                $param['body']['query']['match']['content'] = $params['keyword'];
            }
        }
        if ($params['order'] ==1){
            //文档相关度怕排序
            $param['body']['sort']['_score']['order']='desc';
        }

        $results = $this->client->search($param);
       return $results['hits']['hits'];
    }

    /**
     *删除单一文档
     * id   必须传
     */
    function delete($params)
    {
        $index = $params['index'] ? $params['index'] : $this->config['index'];
        $doc = $params['doc'] ? $params['doc'] : 'content';
        $param =[
            'index' => $index,
            'type' => $doc,
            'id'=>$params['id']
        ];
        $results = $this->client->delete($param);
        return $results;
     }
     /**
      * 检索一个文档是否存在
      */
     public function exitse($params){
         $index = $params['index'] ? $params['index'] : $this->config['index'];
         $doc = $params['doc'] ? $params['doc'] : 'content';
         $param =[
             'index' => $index,
             'type' => $doc,
             'id'=>$params['id']
         ];
         $results = $this->client->exists($param);
         return $results;
     }

    /**
     * 文档更新
     */
     public function update($params){
         $index = $params['index'] ? $params['index'] : $this->config['index'];
         $doc = $params['doc'] ? $params['doc'] : 'content';
         $param =[
             'index' => $index,
             'type' => $doc,
             'id'=>$params['id'],
             'body'=>[
                 'doc' => $params['body']
             ]
         ];
         $results = $this->client->update($param);
         return $results;
     }

}