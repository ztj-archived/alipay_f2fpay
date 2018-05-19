<?php
/**
 * 交易退款
 */

header("Content-type: text/html; charset=utf-8");

if (empty($_GET['out_trade_no'])) {
    exit('请输入订单编号！');
} else {
    $out_trade_no = $_GET['out_trade_no'];
}
if (empty($_GET['refund_amount'])) {
    exit('请输入退款金额！');
} else {
    $refund_amount = $_GET['refund_amount'];
}
if (empty($_GET['out_request_no'])) {
    $out_request_no = $_GET['out_trade_no'] . mt_rand(100, 999);
} else {
    $out_request_no = $_GET['out_request_no'];
}

require_once './../../alipay_sdk/AopSdk.php';
require_once './../../f2fpay_sdk/F2fpaySdk.php';
$F2fpaySdk = new F2fpaySdk(include('../config/config.php'));

if ($F2fpaySdk->refund($out_trade_no, $refund_amount, $out_request_no)) {
    var_dump('退款成功');
} else {
    var_dump('退款失败');
    var_dump($F2fpaySdk->getErrors());
}
