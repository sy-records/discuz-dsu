# discuz-dsu

`Discuz!`论坛的`DSU`签到系统自动签到脚本

## 使用方式

1、补充对应参数
```php
$user = ''; //用户名
$pwd = ''; //密码
$baseUrl = ''; //论坛首页地址 结尾带上”/”
$key = ''; // 方糖key 地址：http://sc.ftqq.com/?c=code

//心情：开心，难过，郁闷，无聊，怒，擦汗，奋斗，慵懒，衰
//{"kx","ng","ym","wl","nu","ch","fd","yl","shuai"};
$qdxq = 'kx'; //签到时使用的心情
$todaysay = "沈唁志 https://qq52o.me"; //想说的话
```
2、手动执行或定时执行

* 手动执行

```bash
# cli 模式下执行
$ php dsuSign.php
```

* Web访问

将`dsuSign.php`放到可访问的网站目录中，通过浏览器访问`http://yourdomain.com/dsuSign.php`

* 定时执行

使用`Linux`的定时任务执行，如`crontab`等

```bash
# crontab -e
0 0 * * * php /www/wwwroot/dsuSign.php  >> /tmp/dsuSign.log 2>&1
```
