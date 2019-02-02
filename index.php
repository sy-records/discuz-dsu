<?php
// 针对的是DSU每日签到插件，路径是plugin.php?id=dsu_paulsign:sign，论坛一般用的都是这个插件

function curlGet($url, $use = false, $save = false, $referer = null, $post_data = null) {
    global $cookie_file;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //需要使用cookies
    if ($use) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    }
    //需要保存cookies
    if ($save) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    }
    //需要referer伪装
    if (isset($referer)) {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }

    //需要post数据
    if (is_array($post_data)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

function getFormhash($res) {
    if (preg_match('/name="formhash" value="(.*?)"/i', $res, $matches)) {
        return $matches[1];
    } else {
        exit('没有找到formhash');
    }
}

//签到代码
$user = ''; //用户名
$pwd = ''; //密码
$baseUrl = ''; //论坛首页地址 结尾带上”/”

//心情：开心，难过，郁闷，无聊，怒，擦汗，奋斗，慵懒，衰
//{"kx","ng","ym","wl","nu","ch","fd","yl","shuai"};
$qdxq = 'kx'; //签到时使用的心情
$todaysay = '开心~~~'; //想说的话

//账号登录地址
$loginPageUrl = $baseUrl . 'member.php?mod=logging&action=login';
//账号信息提交地址
$loginSubmitUrl = $baseUrl . 'member.php?mod=logging&action=login&loginsubmit=yes&loginhash=LNvu3';
//签到页面地址
$signPageUrl = $baseUrl . 'plugin.php?id=dsu_paulsign:sign';
//签到信息提交地址
$signSubmitUrl = $baseUrl . 'plugin.php?id=dsu_paulsign:sign&operation=qiandao&infloat=0&inajax=0';

//存放Cookies的文件
$cookie_file = tempnam('./temp', 'cookie');

//访问论坛登录页面，保存Cookies
$res = curlGet($loginPageUrl, false, true);
//获取DiscuzX论坛的formhash验证串
$formhash = getFormhash($res);

//构建登录信息
$login_array = array(
    'username' => $user,
    'password' => $pwd,
    'referer' => $baseUrl,
    'questionid' => 0,
    'answer' => '',
    'formhash' => $formhash,
);

//携带cookie提交登录信息
$res = curlGet($loginSubmitUrl, true, true, null, $login_array);
if (strpos($res, '欢迎您回来')) {
    //访问签到页面
    $res = curlGet($signPageUrl, true, true);
    //根据签到页面上的文字来判断今天是否已经签到
    if (strpos($res, '您今天已经签到过了或者签到时间还未开始')) {
        $resultStr = "今天已签过到\r\n";
    } else {
        //获取formhash验证串
        $formhash = getFormhash($res);
        //构造签到信息
        $post_data = array(
            'qdmode' => 1,
            'formhash' => $formhash,
            'qdxq' => $qdxq,
            'fastreply' => 0,
            'todaysay' => $todaysay,
        );
        //提交签到信息
        $res = curlGet($signSubmitUrl, true, true, null, $post_data);
        if (strpos($res, '签到成功')) {
            $resultStr = "签到成功\r\n";
        } else {
            $resultStr = "签到失败\r\n";
        }
    }
} else {
    $resultStr = "登陆失败\r\n";
}
echo $resultStr;
unlink($cookie_file); //删除cookie文件
