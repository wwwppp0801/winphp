<?php

trait WxPay{
    public function wxPayAction(){
        require_once ("_wxpay/RequestHandler.class.php");
        require_once ("_wxpay/tenpay_config.php");
        require_once ("_wxpay/ResponseHandler.class.php");
        require ("_wxpay/client/TenpayHttpClient.class.php");


        $payment_id=$this->_POST("payment_id","",'99999');
        $payment=new Payment();
        $payment = $payment->addWhere("id",$payment_id)->select();
        if(!$payment || $payment->mStatus != 'wait_pay'){
            return [$this->_GET('data_type', 'json').":",AppUtils::returnValue(['msg'=>'payment\'s stauts error'],'99999')];
        }

        $order=new Order();
        $order=$order->addWhere("id",$payment->mOrderId)->select();
        if(!$order){
            return [$this->_GET('data_type', 'json').":",AppUtils::returnValue(['msg'=>'order not exists'],'99999')];
        }

        $stock = new Stock();
        $stock = $stock->addWhere('id', $order->mStockId)->select();
        if(!$order){
            return [$this->_GET('data_type', 'json').":",AppUtils::returnValue(['msg'=>'stock not exists'],'99999')];
        }
        $stockAmount = new StockAmount();
        $stockAmount = $stockAmount->addWhere('id', $order->mStockAmountId)->select();
        if(!$stockAmount){
            return [$this->_GET('data_type', 'json').":",AppUtils::returnValue(['msg'=>'stockAmount not exists'],'99999')];
        }



        //获取提交的订单号
        $out_trade_no=GlobalMethod::genOrderId($payment->mId);
        $outparams =array();
        //商品价格（包含运费），以分为单位
        $total_fee= $payment->mAmount*100;
        //获取token值
        $reqHandler = new RequestHandler();
        $reqHandler->init($APP_ID, $APP_SECRET, $PARTNER_KEY, $APP_KEY);
        $Token= $reqHandler->GetToken();
        if ( $Token !='' ){
            //=========================
            //生成预支付单
            //=========================
            //设置packet支付参数
            $packageParams =array();		

            $packageParams['bank_type']		= 'WX';	            //支付类型
            $packageParams['body']			= $stock->mName." ".$stockAmount->mSkuValue;					//商品描述
            $packageParams['fee_type']		= '1';				//银行币种
            $packageParams['input_charset']	= 'UTF-8';		    //字符集
            $packageParams['notify_url']	= $notify_url;	    //通知地址
            $packageParams['out_trade_no']	= $out_trade_no;		        //商户订单号
            $packageParams['partner']		= $PARTNER;		        //设置商户号
            $packageParams['total_fee']		= $total_fee;			//商品总金额,以分为单位
            $packageParams['spbill_create_ip']= $_SERVER['REMOTE_ADDR'];  //支付机器IP
            //获取package包
            $package= $reqHandler->genPackage($packageParams);
            $time_stamp = time();
            $nonce_str = md5(rand());
            //设置支付参数
            $signParams =array();
            $signParams['appid']	=$APP_ID;
            $signParams['appkey']	=$APP_KEY;
            $signParams['noncestr']	=$nonce_str;
            $signParams['package']	=$package;
            $signParams['timestamp']=$time_stamp;
            $signParams['traceid']	= $payment->mId."_".$order->mId."_".$time_stamp;

            //生成支付签名
            $sign = $reqHandler->createSHA1Sign($signParams);
            //增加非参与签名的额外参数
            $signParams['sign_method']		='sha1';
            $signParams['app_signature']	=$sign;
            //var_dump($signParams);
            //剔除appkey
            unset($signParams['appkey']); 
            //获取prepayid
            $prepayid=$reqHandler->sendPrepay($signParams);

            if ($prepayid != null) {
                $pack	= 'Sign=WXPay';
                //输出参数列表
                $prePayParams =array();
                $prePayParams['appid']		=$APP_ID;
                $prePayParams['appkey']		=$APP_KEY;
                $prePayParams['noncestr']	=$nonce_str;
                $prePayParams['package']	=$pack;
                $prePayParams['partnerid']	=$PARTNER;
                $prePayParams['prepayid']	=$prepayid;
                $prePayParams['timestamp']	=$time_stamp;
                //生成签名
                $sign=$reqHandler->createSHA1Sign($prePayParams);

                $outparams['retcode']=0;
                $outparams['retmsg']='ok';
                $outparams['appid']=$APP_ID;
                $outparams['noncestr']=$nonce_str;
                $outparams['package']=$pack;
                $outparams['prepayid']=$prepayid;
                $outparams['timestamp']=$time_stamp;
                $outparams['sign']=$sign;

            }else{
                $outparams['retcode']=-2;
                $outparams['retmsg']='错误：获取prepayId失败';
            }
        }else{
            $outparams['retcode']=-1;
            $outparams['retmsg']='错误：获取不到Token';
        }


    /**
    =========================
    输出参数列表
    =========================
     */
        //Json 输出
        //debug信息,注意参数含有特殊字符，需要JsEncode
        //
        if ($DEBUG_ ){
            echo PHP_EOL  .'/*' . ($reqHandler->getDebugInfo()) . '*/';
        }
        if($outparams['retcode']!==0){
            return [$this->_GET('data_type', 'json').":",AppUtils::returnValue(['msg'=>$outparams['retmsg']],'99999')];
        }
        $outparams['partnerid']=$PARTNER;
        return [$this->_GET('data_type', 'json').":",AppUtils::returnValue($outparams)];
        //return ["text:".json_encode($outparams)];
    }
    public function wxNotifyAction(){

        //---------------------------------------------------------
        //即时到帐支付后台回调示例，商户按照此文档进行开发即可
        //---------------------------------------------------------
        Logger::debug("wx notify post:".json_encode($_POST));
        Logger::debug("wx notify get:".json_encode($_GET));

        require ("_wxpay/ResponseHandler.class.php");
        require ("_wxpay/RequestHandler.class.php");
        require ("_wxpay/client/TenpayHttpClient.class.php");
        require ("_wxpay/function.php");
        require_once ("_wxpay/tenpay_config.php");

        //log_result("进入后台回调页面");

        /* 创建支付应答对象 */
        $resHandler = new ResponseHandler();
        $resHandler->setKey($PARTNER_KEY);

        //初始化页面提交过来的参数

            //判断签名
        if($resHandler->isTenpaySign() == true) {
            //商户在收到后台通知后根据通知ID向财付通发起验证确认，采用后台系统调用交互模式	
            $notify_id = $resHandler->getParameter("notify_id");//通知id

            //商户交易单号
            $out_trade_no = $resHandler->getParameter("out_trade_no");

            //财付通订单号
            $transaction_id = $resHandler->getParameter("transaction_id");

            //商品金额,以分为单位
            $total_fee = $resHandler->getParameter("total_fee");

            //如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
            $discount = $resHandler->getParameter("discount");

            //支付结果
            $trade_state = $resHandler->getParameter("trade_state");
            //可获取的其他参数还有
            //bank_type			银行类型,默认：BL
            //fee_type			现金支付币种,目前只支持人民币,默认值是1-人民币
            //input_charset		字符编码,取值：GBK、UTF-8，默认：GBK。
            //partner			商户号,由财付通统一分配的10位正整数(120XXXXXXX)号
            //product_fee		物品费用，单位分。如果有值，必须保证transport_fee + product_fee=total_fee
            //sign_type			签名类型，取值：MD5、RSA，默认：MD5
            //time_end			支付完成时间
            //transport_fee		物流费用，单位分，默认0。如果有值，必须保证transport_fee +  product_fee = total_fee

            //判断签名及结果
            if ("0" == $trade_state){
                //----------------------
                //即时到帐处理业务开始
                //-----------------------
                //处理数据库逻辑
                //注意交易单不要重复处理
                //注意判断返回金额
                //-----------------------
                //即时到帐处理业务完毕
                //-----------------------
                //给财付通系统发送成功信息，给财付通系统收到此结果后不在进行后续通知
                //log_result("后台通知成功");
                $this->_changeOrderStatus($out_trade_no, $transaction_id, $trade_state, $buyer_account="", 'wx_client');
            } else {
                //log_result("后台通知失败");
            }
            //回复服务器处理成功
            Logger::debug($resHandler->getDebugInfo());
            return ["text:Success"];
        } else {
            Logger::debug($resHandler->getDebugInfo());
            return ["text:<br/>" . "验证签名失败" . "<br/>".$resHandler->getDebugInfo() . "<br>"];
        }
    }
    private function _changeOrderStatus($out_trade_no , $trade_no, $trade_status, $pay_account, $source='zfb') {
        $payment_id= GlobalMethod::genPaymentId($out_trade_no); 
        $payment = new Payment();
        $payment = $payment->addWhere("id",$payment_id)->select();
        if($payment && $payment->mStatus == 'wait_pay'){
            $payment->mStatus = 'payed';
            $payment->mUpdateTime = time();
            $payment->mTradeNo = $out_trade_no; 
            $payment->mPayAccount = $pay_account;
            $payment->mPlatformTradeNo = $trade_no;
            $payment->mSource = $source;
            if($payment->save()){
                $order=new Order();
                $order=$order->addWhere("id",$payment->mOrderId)->select();
                $stock = new Stock();
                $stock = $stock->addWhere('id', $order->mStockId)->select();
                if($order){
                    if($payment->mType=='prepay'){
                        $order->mStatus='prepayed';
                        # 预付之后将锁定数量写入销售数量
                        $num = $order->mNum;
                        $stock_amount_id = $order->mStockAmountId;
                        $stock_amount_tbl=new DBTable('stock_amount');
                        $res=$stock_amount_tbl->addWhere('id',$stock_amount_id)->update(['locked_amount'=>["`locked_amount`-$num",DBTable::NO_ESCAPE],'sold_amount'=>["`sold_amount`+$num",DBTable::NO_ESCAPE]]);
                        $notifyTpl = "您的%stockName%已成功预定，（2小时~5个工作日）买手买到后会通知您补款，买手没能成功采购，定金会退回给您，客服咨询4008766388";
                    }else{
                        $order->mStatus='payed';
                        $notifyTpl = "您%stockName%的余款已经补齐，买手将会在3个工作日内对商品进行打包，并从海外寄出。客服咨询4008766388";
                    }
                    $order->mUpdateTime = time();
                    $order->save();
                    $notifyStr = str_replace('%stockName%', $stock ? "“".$stock->mName."”" : "", $notifyTpl);
                    Notification::sendNotification($order->mUserId,['title'=>$notifyStr,'type'=>'trade','from'=>'trade',
                        'data'=>[
                            'order_id'=>$order->mId,
                            'trade_title'=>$order->statusDesc(),
                            'stock_imageUrl'=>$stock->mImgs?json_decode($stock->mImgs,true)[0]:[],
                        ]
                    ]);
                    GlobalMethod::orderLog($order, '', 'user', $order->mUserId);
                }
            }
        }
    }
}
