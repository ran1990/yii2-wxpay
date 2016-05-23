# 微信支付PHP SDK
微信支付PHP SDK 优化版本，支持Composer, PSR4

**基于微信官方的SDK 改编而来**

- 生成支付二维码
  
```
       $notify = new NativePay();
       
       $url = $notify->GetPrePayUrl($product_id);
       return Html::img(QrCode::png($url,false,'L',6,0));
```
- 调用微信H5支付

```
 //统一下单
 
        $tools = new JsApiPay();
         
        $openId = $tools->GetOpenid();
        
        $input = new WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetOut_trade_no($out_trade_no);//订单号
        $input->SetTotal_fee($total_fee * 100);//金额
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 300));
        $input->SetNotify_url($notify_url);//异步通知
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);
        $order = WxPayApi::unifiedOrder($input);
        try {
            $jsApiParameters = $tools->GetJsApiParameters($order);
        } catch (Exception $e) {
            throw  new NotFoundHttpException();
        }
       
       //调用H5支付
        return $this->controller->render('wxpay', [
            'jsApiParameters' => $jsApiParameters,
            'success_url' => $this->successUrl.$data->order_info_id,//支付成功跳转地址
            'fail_url'=>$this->failUrl,//取消支付跳转地址
        ]);
```
- 扫描支付（模式一）

```
  //服务层接收
  class NativeNotifyCallBack extends  WxPayNotify
    {
        public function unifiedorder($openId, $product_id)
        {
            //orderInfo 的order_id怎么获取
        
            // 获取订单信息
            $order_info = Payment::getOrderInfoByPramas(['order_info_id'=>$product_id]);
        
            //统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody($body);
            //$input->SetAttach("test");
            $input->SetOut_trade_no($order_info->order_sn);
            $input->SetTotal_fee($order_info->order_amount * 100);
            //$input->SetTime_start(date("YmdHis"));
            //$input->SetTime_expire(date("YmdHis", time() + 600));
            //$input->SetGoods_tag("test");
            $input->SetNotify_url(\Yii::$app->params['wxpay']['notify_url']);
            $input->SetTrade_type("NATIVE");
            $input->SetOpenid($openId);
            $input->SetProduct_id($product_id);
            $result = WxPayApi::unifiedOrder($input);
        
            return $result;
            
        }
        
        public function NotifyProcess($data, &$msg)
        {
            //echo "处理回调";
            if(!array_key_exists("openid", $data) ||
                !array_key_exists("product_id", $data))
            {
                $msg = "回调数据异常";
                return false;
            }
        
            $openid = $data["openid"];
            $product_id = $data["product_id"];
        
            //统一下单
            $result = $this->unifiedorder($openid, $product_id);
            if(!array_key_exists("appid", $result) ||
                !array_key_exists("mch_id", $result) ||
                !array_key_exists("prepay_id", $result))
            {
                $msg = "统一下单失败";
                return false;
            }
        
            $this->SetData("appid", $result["appid"]);
            $this->SetData("mch_id", $result["mch_id"]);
            $this->SetData("nonce_str", WxPayApi::getNonceStr());
            $this->SetData("prepay_id", $result["prepay_id"]);
            $this->SetData("result_code", "SUCCESS");
            $this->SetData("err_code_des", "OK");
            
            return true;
        }  
    }
    
    //控制器调用
        $notify = new NativeNotifyCallBack();
        $notify->Handle(true);

```