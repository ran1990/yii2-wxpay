<?php
namespace Opwechat\Phppayment;

/** 
 * 配置账号信息
 */

class WxPayConfig
{
    private $config = [];
    
    protected static $_instance = null;
    
    protected   function __construct(){
        
        //APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        $this->config['appid'] =  \Yii::$app->params['wxpay']['appid'];
        
        //MCHID：商户号（必须配置，开户邮件中可查看）
        $this->config['mchid'] =  \Yii::$app->params['wxpay']['mchid'];
        
        //KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        $this->config['mchkey'] =  \Yii::$app->params['wxpay']['mchkey'];
        
        // APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
        $this->config['appsecret'] =  \Yii::$app->params['wxpay']['appsecret'];
        
        //=======【证书路径设置】=====================================
        /**
         * TODO：设置商户证书路径
         * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
         * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
         * @var path
         */
        
        $this->config['sslcert_path'] =  \Yii::$app->params['wxpay']['sslcert_path'];
        $this->config['sslkey_path'] =  \Yii::$app->params['wxpay']['sslkey_path'];
        
        //=======【curl代理设置】===================================
        /**
         * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
         * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
         * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
         * @var unknown_type
         */
        
        $this->config['curl_proxy_host'] =  \Yii::$app->params['wxpay']['curl_proxy_host'];
        $this->config['curl_proxy_port'] =  \Yii::$app->params['wxpay']['curl_proxy_port'];
        
        //=======【上报信息配置】===================================
        /**
         * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
         * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
         * 开启错误上报。
         * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
         * @var int
         */
        $this->config['report_levenl'] =  \Yii::$app->params['wxpay']['report_levenl'];
        
    }
    
    /**
     * 单例模式，唯一入口
     */
    public static  function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        
        return self::$_instance->config;
    }
    
    
}
