<?php
const DS = '/';
const DOMAIN = 'game.com';                    //主域名
const ADMIN_URL = 'http://admin.'.DOMAIN.DS;   //后台网址
const WEB_URL = 'http://www.'.DOMAIN.DS;
const API_URL = 'http://api.'.DOMAIN.DS;   //接口地址
const UPLOAD_URL = 'http://192.168.13.209:8083/'; //静态文件地址
const SESSION_PREFIX = '_cms_';  //Session缓存前缀
const LOGIN_TIME = 6000;
const IMG_SIZE = 10 * 1024 * 1024;
const JWT_KEY = 'http://api.\'.DOMAIN.DS';
const DEV = true;   //开发环境
const MYSQL_RED = false;   //是否开启DB读写分离
