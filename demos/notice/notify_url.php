<?php
/**
 * 异步通知
 */

require_once './../../alipay_sdk/AopSdk.php';
require_once './../../f2fpay_sdk/F2fpaySdk.php';
$F2fpaySdk = new F2fpaySdk(include('../config/config.php'));
$result = $F2fpaySdk->getConfig();

//验证 out_trade_no 是否为商户系统中创建的订单号
//判断 total_amount 是否确实为该订单的实际金额
//校验 seller_id | seller_email 是否为 out_trade_no 这笔单据的对应的操作方
//验证 app_id 是否为该商户本身

if ($result) {
    //商户订单号
    $out_trade_no = $_POST['out_trade_no'];
    //支付宝交易号
    $trade_no = $_POST['trade_no'];
    //交易状态
    $trade_status = $_POST['trade_status'];
    //交易完成
    if ($_POST['trade_status'] == 'TRADE_FINISHED') {
        //判断该笔订单是否在商户网站中已经做过处理
        //判断交易金额是否一致
    } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
        //判断交易金额是否一致
    }
    echo "success";

} else {
    echo "fail";
}
