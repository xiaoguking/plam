<?php
const DS = '/';
const DOMAIN = 'xg.com';                    //主域名
const API_URL = 'http://api.'.DOMAIN.DS;   //接口地址
const UPLOAD_URL = 'http://upload.'.DOMAIN.DS; //静态文件地址
const SESSION_PREFIX = '_cms_';  //Session缓存前缀
const IMG_SIZE = 10 * 1024 * 1024;
const JWT_KEY = 'http://api.\'.DOMAIN.DS';
const DEV = true;   //开发环境
const MYSQL_RED = false;   //是否开启DB读写分离
