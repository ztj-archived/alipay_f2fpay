<?php
/**
 * 当面付调用入口
 */

class F2fpaySdk
{
    protected $config = [];
    protected $errors = [];

    public function __construct(array $config)
    {
        $this->loadFiles();
        $this->config = $this->buildConfig($config);
    }

    /**
     * 载入文件
     */
    private function loadFiles()
    {
        require_once __DIR__ . '/model/result/AlipayF2FPayResult.php';
        require_once __DIR__ . '/model/result/AlipayF2FQueryResult.php';
        require_once __DIR__ . '/model/result/AlipayF2FRefundResult.php';
        require_once __DIR__ . '/model/result/AlipayF2FPrecreateResult.php';

        require_once __DIR__ . '/model/builder/AlipayTradeQueryContentBuilder.php';
        require_once __DIR__ . '/model/builder/AlipayTradeCancelContentBuilder.php';
        require_once __DIR__ . '/model/builder/AlipayTradePrecreateContentBuilder.php';
        require_once __DIR__ . '/model/builder/AlipayTradePayContentBuilder.php';
        require_once __DIR__ . '/model/builder/AlipayTradeRefundContentBuilder.php';
        require_once __DIR__ . '/service/AlipayTradeService.php';
    }

    /**
     * @param array $config 配置
     * @return array
     */
    private function buildConfig(array $config)
    {
        $defaultConfig = [
            'sign_type' => 'RSA2',
            'charset' => 'UTF-8',
            'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
            'MaxQueryRetry' => '10',
            'QueryDuration' => '3'
        ];
        return array_merge($defaultConfig, $config);
    }

    /**
     * 调用支付宝服务
     * @return AlipayTradeService
     */
    public function alipay_trade_service()
    {
        return new AlipayTradeService($this->config);
    }

    /**
     * 通知校验
     * @param $post
     * @return bool
     */
    public function notice_check($params)
    {
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = $this->config['alipay_public_key'];
        return $aop->rsaCheckV1($params, $this->config['alipay_public_key'], $this->config['sign_type']);
    }

    /**
     * 获取配置
     * @return array
     */
    public function get_config()
    {
        return $this->config;
    }

    /**
     * 构建二维码
     * @param string $out_trade_no 订单号
     * @param string $subject 订单标题
     * @param string $total_amount 订单金额
     * @return bool
     */
    public function build_qrcode($out_trade_no, $subject, $total_amount)
    {
        $qrPayRequestBuilder = new AlipayTradePrecreateContentBuilder();
        $qrPayRequestBuilder->setOutTradeNo($out_trade_no);
        $qrPayRequestBuilder->setTotalAmount($total_amount);
        $qrPayRequestBuilder->setSubject($subject);

        $AlipayTradeService = $this->alipay_trade_service();
        $QrPayResult = $AlipayTradeService->qrPay($qrPayRequestBuilder);

        if ($QrPayResult->getTradeStatus() == 'SUCCESS') {
            $response = $QrPayResult->getResponse();
            return $response->qr_code;
        } else {
            $this->setError($QrPayResult->getResponse());
            return false;
        }
    }

    /**
     * 查询订单状态
     * @param string $out_trade_no 订单号
     * @return bool
     */
    public function query_trade_state($out_trade_no)
    {
        $queryContentBuilder = new AlipayTradeQueryContentBuilder();
        $queryContentBuilder->setOutTradeNo($out_trade_no);

        $AlipayTradeService = $this->alipay_trade_service();
        $QueryResult = $AlipayTradeService->queryTradeResult($queryContentBuilder);

        if ($QueryResult->getTradeStatus() == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 查询订单资源
     * @param string $out_trade_no 订单号
     * @return mixed
     */
    public function query_trade_response($out_trade_no)
    {
        $queryContentBuilder = new AlipayTradeQueryContentBuilder();
        $queryContentBuilder->setOutTradeNo($out_trade_no);

        $AlipayTradeService = $this->alipay_trade_service();
        $QueryResult = $AlipayTradeService->queryTradeResult($queryContentBuilder);

        return $QueryResult->getResponse();
    }

    /**
     * 订单退款
     * @param string $out_trade_no 订单编号
     * @param string $refund_amount 退款金额
     * @param string $out_request_no 退款请求编号
     * @return bool
     */
    public function refund($out_trade_no, $refund_amount, $out_request_no)
    {
        $refundRequestBuilder = new AlipayTradeRefundContentBuilder();
        $refundRequestBuilder->setOutTradeNo($out_trade_no);
        $refundRequestBuilder->setRefundAmount($refund_amount);
        $refundRequestBuilder->setOutRequestNo($out_request_no);

        $AlipayTradeService = $this->alipay_trade_service();
        $RefundResult = $AlipayTradeService->refund($refundRequestBuilder);

        if ($RefundResult->getTradeStatus() == 'SUCCESS') {
            return true;
        } else {
            $this->setError($RefundResult->getResponse());
            return false;
        }
    }

    protected function setError($data)
    {
        $this->errors[] = $data;
    }

    public function getError()
    {
        return current($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
