<?php
/**
 * 简单的二维码订单生成
 */

header('Content-type: text/html; charset=utf-8');

//订单编号
$outTradeNo = date('Ymdhis') . mt_rand(100, 999);
//订单标题
$subject = '订单' . $outTradeNo;
//订单总金额
$totalAmount = '1.00';

echo 'out_trade_no=' . $outTradeNo . '<br>';

require_once './../../alipay_sdk/AopSdk.php';
require_once './../../f2fpay_sdk/F2fpaySdk.php';
$F2fpaySdk = new F2fpaySdk(include('../config/config.php'));
$qrcode = $F2fpaySdk->build_qrcode($outTradeNo, $subject, $totalAmount);
if ($qrcode) {
    echo '<img src="https://pan.baidu.com/share/qrcode?w=200&h=200&url=' .
        urlencode($qrcode) . '"  widht="200" height="200" />';
} else {
    var_dump('二维码创建失败');
    var_dump($F2fpaySdk->getErrors());
}
