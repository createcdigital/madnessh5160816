<?php

    // get POST parameters
    $nickname         = $data['attach'];
    $openid        = $data['openid'];
    $outtradeno    = $data['out_trade_no']."|".$data['transaction_id'];
    $paystatus     = 1; // 已支付

    $errormsg = "";
    if(isset($nickname) && isset($openid) && isset($outtradeno))
    {

        if ($stmt = $mysqli->prepare("SELECT id, outtradeno FROM user WHERE openid = ?")) {

            /* bind parameters for markers */
            $stmt->bind_param("s", $openid);

            /* execute query */
            $stmt->execute();

            $stmt->bind_result($source_id, $source_outtradeno);

            /* fetch values */
            while ($stmt->fetch()) {
                 $source_id = $source_id;
                 $source_outtradeno = $source_outtradeno;
             }

            if(!isset($source_outtradeno) || $source_outtradeno === "")
            {
                $numbers = 1;

                if ($stmt1 = $mysqli->prepare("UPDATE user SET paystatus=?, outtradeno=? , numbers=? WHERE openid=?")) {

                    // Bind the variables to the parameter as strings.
                    $stmt1->bind_param("ssss", $paystatus, $outtradeno, $numbers, $openid);

                    // Execute the statement.
                    if($stmt1->execute())
                        Log::DEBUG("notify to db: success! openid:".$openid."&url=");
                    else
                    {
                        $errormsg = '准备预执行T-SQL脚本发生错误！';
                    }


                }else
                {
                    $errormsg = '准备预执行T-SQL脚本发生错误！';
                }
            }else
                {
                    $errormsg = 'Payment status has been updated！';
                }
        }else
        {
            $errormsg = '准备预执行T-SQL脚本发生错误！';
        }

        /* close statement */
        $mysqli->commit();
        $stmt->close();

    }else
    {
        $errormsg = '请求参数nickname&outtradeno不能为空!';
    }

    if($errormsg !== "")
        Log::DEBUG("notify to db: fail! openid:".$openid.", description:".$errormsg);

    /* close connection */
    $mysqli->close();
?>
