<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
</head>
<style>* {
    padding: 0;
    margin: 0
}

a {
    text-decoration: none
}

.notfoud-container .img-404 {
    text-align: center;
    margin-top: 40px;
    margin-bottom: 20px
}

.notfoud-container .notfound-p {
    line-height: 22px;
    font-size: 17px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f6f6f6;
    text-align: center;
    color: #262b31
}

.notfoud-container .notfound-reason {
    color: #9ca4ac;
    text-align: left;
    margin: 0 auto
}

.notfoud-container .notfound-reason p {

    font-size: 23px;
    color: #333333;
    font-family: "Microsoft YaHei";
    font-weight: bold;
    text-align: center;
    line-height: 28px;
    margin-top: 13px
}

.notfoud-container .notfound-reason ul li {
    margin-top: 10px;
    margin-left: 36px
}

.notfoud-container .notfound-btn-container {
    margin: 40px auto 0;
    text-align: center
}

.notfoud-container .notfound-btn-container .notfound-btn {
    width: 160px;
    margin-right: 20px;
    border: 1px solid #FF6C60;
    background-color: #FF6C60;
    color: #fff;
    font-size: 15px;
    border-radius: 100px;
    text-align: center;
    padding: 10px 40px;
    line-height: 40px;
}
.notfoud-container .notfound-btn-container .not-btn {
    width: 160px;
    border: 1px solid #6BCACA;
    background-color: #6BCACA;
    color: #fff;
    font-size: 15px;
    border-radius: 100px;
    text-align: center;
    padding: 10px 32px;
    line-height: 40px;
}</style>

</head>
<body>

<div class="notfoud-container" style="margin-top: 180px">
    <div class="img-404">
<!--        <img src="<?= $this->url->get('images/error.jpg') ?>" style="width: 100px;border-radius: 120px">-->
    </div>
    <div class="notfound-reason">
        <p><?= $magess ?></p>
    </div>
</div>


</body>
</html>