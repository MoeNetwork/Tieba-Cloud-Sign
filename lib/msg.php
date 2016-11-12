<?php
/**
 * 显示错误消息
 * @param string $msg 消息内容
 * @param bool|string bool true=返回上一页|bool false=屏蔽返回链接|string=自定义返回地址
 * @param bool $die 是否终止PHP
 * @param bool $title 是否显示标题
 */
function msg($msg = '未知的异常',$url = true,$die = true,$title = true,$issue = false) {
    if(defined('SYSTEM_ISCONSOLE') && SYSTEM_ISCONSOLE) {
        echo $msg . PHP_EOL;
        die;
    }
    if (defined('SYSTEM_NAME')) {
        $sysname = SYSTEM_NAME;
    } else {
        $sysname = SYSTEM_FN;
    }
    ?>  
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo strip_tags($sysname) ?> - 提示信息</title>
        <style type="text/css">
            html {
                background: #eee;
            }
            body {
                background: #fff;
                color: #333;
                font-family: "微软雅黑","Microsoft YaHei", sans-serif;
                margin: 2em auto;
                padding: 1em 2em;
                max-width: 700px;
                -webkit-box-shadow: 10px 10px 10px rgba(0,0,0,0.13);
                box-shadow: 10px 10px 10px rgba(0,0,0,0.13);
                opacity:0.8;
            }
            h1 {
                border-bottom: 1px solid #dadada;
                clear: both;
                color: #666;
                font: 24px "微软雅黑","Microsoft YaHei",, sans-serif;
                margin: 30px 0 0 0;
                padding: 0;
                padding-bottom: 7px;
            }
            #error-page {
                margin-top: 50px;
            }
            h3 {
                text-align:center;
            }
            #error-page p {
                font-size: 9px;
                line-height: 1.5;
                margin: 25px 0 20px;
            }
            #error-page code {
                font-family: Consolas, Monaco, monospace;
            }
            ul li {
                margin-bottom: 10px;
                font-size: 9px ;
            }
            a {
                color: #21759B;
                text-decoration: none;
                margin-top:-10px;
            }
            a:hover {
                color: #D54E21;
            }
            .button {
                background: #f7f7f7;
                border: 1px solid #cccccc;
                color: #555;
                display: inline-block;
                text-decoration: none;
                font-size: 9px;
                line-height: 26px;
                height: 28px;
                margin: 0;
                padding: 0 10px 1px;
                cursor: pointer;
                -webkit-border-radius: 3px;
                -webkit-appearance: none;
                border-radius: 3px;
                white-space: nowrap;
                -webkit-box-sizing: border-box;
                -moz-box-sizing:    border-box;
                box-sizing:         border-box;

                -webkit-box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
                box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
                vertical-align: top;
            }

            .button.button-large {
                height: 29px;
                line-height: 28px;
                padding: 0 12px;
            }

            .button:hover,
            .button:focus {
                background: #fafafa;
                border-color: #999;
                color: #222;
            }

            .button:focus  {
                -webkit-box-shadow: 1px 1px 1px rgba(0,0,0,.2);
                box-shadow: 1px 1px 1px rgba(0,0,0,.2);
            }

            .button:active {
                background: #eee;
                border-color: #999;
                color: #333;
                -webkit-box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
                box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
            }
            table {
                table-layout:auto;
                border:1px solid #333;
                empty-cells:show;
                border-collapse:collapse;
            }
            th {
                padding:4px;
                border:1px solid #333;
                overflow:hidden;
                color:#333;
                background: #eee;
            }
            td {
                padding:4px;
                border:1px solid #333;
                overflow:hidden;
                color:#333;
            }
        </style>
    </head>
    <body id="error-page">
        <?php
		if ($title) echo '<h3>'.$sysname.' - 提示信息</h3><br/>';
        echo $msg;
		if ($issue) echo '<br/><a style="float:left" href="http://git.oschina.net/kenvix/Tieba-Cloud-Sign/issues" target="_blank">反馈该问题</a><br/>';
		if ($url !== false) {
            if ($url === true) {
                echo '<br/><a style="float:right" href="javascript:history.back(-1)"><< 返回上一页</a>';
            } else {
                echo '<br/><a style="float:right" href="'.$url.'"><< 返回上一页</a>';
            }
        } 
        ?>
        <br/>
    </body>
    </html>
    <?php
    $die && die;
}