<?php
/**
 * 交易结果查询
 */

header("Content-type: text/html; charset=utf-8");

if (empty($_GET['out_trade_no'])) {
    exit('请输入订单编号！');
} else {
    $out_trade_no = $_GET['out_trade_no'];
}

require_once './../../alipay_sdk/AopSdk.php';
require_once './../../f2fpay_sdk/F2fpaySdk.php';
$F2fpaySdk = new F2fpaySdk(include('../config/config.php'));

var_dump($F2fpaySdk->query_trade_response($out_trade_no));

if ($F2fpaySdk->query_trade_state($out_trade_no)) {
    echo 'out_trade_no=' . $out_trade_no . '&refund_amount=1<br>';
    var_dump('支付成功');
} else {
    var_dump('支付失败');
}
