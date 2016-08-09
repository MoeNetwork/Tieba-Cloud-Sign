<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/**
 * 异常类
 * 抛出异常：throw new E('异常',异常码) 或者 throw new Exception('异常',异常码)
 */
class E extends Exception {

    public function __tostring() {
        self::display($this->code,$this->message,$this->file,$this->line,$this->getTrace());
    }

    public static function exception($e) {
        $ex = new ReflectionClass($e);
        self::display(
            $ex->getMethod('getCode')->invoke($e),
            $ex->getMethod('getMessage')->invoke($e),
            $ex->getMethod('getFile')->invoke($e),
            $ex->getMethod('getLine')->invoke($e),
            $ex->getMethod('getTrace')->invoke($e)
        );
    }

    public static function error($errno, $errstr, $errfile, $errline) {
        $errnoo = self::getErrorType($errno);
        if (SYSTEM_DEV == true && !defined('SYSTEM_NO_ERROR')) {
            echo '<div class="alert alert-danger alert-dismissable">';
            echo '<strong>'.$errnoo.'：</strong>'.$errstr.'<br/>文件：'.$errfile.' @ '.$errline.'行</div>';
        }
        if (function_exists('doAction')) {
            doAction('error', $errno, $errstr, $errfile, $errline, $errnoo);
        }
    }

    public static function display($code , $message , $file , $line , $trace) {
        ob_clean();
        $msg = SYSTEM_FN . ' V' . SYSTEM_VER . ' 在工作时发生致命的异常 @ '.date('Y-m-d H:m:s').'<br/><b>消息：</b>#' . $code . ' - ' . $message .'<br/><br/>';
        $msg .= '<table style="width:100%"><thead><th>文件</th><th>行</th><th>代码</th></thead><tbody>';
        $msg .= '<tr><td>' . $file . '</td><td>' . $line . '' . '</td><td>[抛出异常]</td></tr>';
        foreach ($trace as $v) {
            $tracefile = isset($v['file']) ? $v['file'] : '';
            $traceline = isset($v['line']) ? $v['line'] : '';
            $msg .= '<tr><td>' . $tracefile . '</td><td>' .  $traceline . '</td><td>' . $v['function'] . '</td></tr>';
        }
        $msg .= '</tbody></table>';
        if (function_exists('doAction')) {
            doAction('error_2',$code,$message,$file,$line,$trace);
        }
        msg($msg);
    }

    /**
     * 获取错误类型
     * @param int $errno 错误代码
     * @return string 错误类型
     */
    public static function getErrorType($errno) {
        switch ($errno) {
                case E_ERROR:               case E_USER_ERROR:      $errnoo = '致命错误';                  break;
                case E_WARNING:             case E_USER_WARNING:    $errnoo = '警告';                      break;
                case E_PARSE:                                       $errnoo = '解析错误';                  break;
                case E_NOTICE:              case E_USER_NOTICE:     $errnoo = '提示';                      break;
                case E_CORE_ERROR:                                  $errnoo = '核心错误';                  break;
                case E_CORE_WARNING:                                $errnoo = '核心警告';                  break;
                case E_COMPILE_ERROR:                               $errnoo = '编译错误';                  break;
                case E_COMPILE_WARNING:                             $errnoo = '编译警告';                  break;
                case E_STRICT:                                      $errnoo = '严谨性提示';                break;
                default:                                            $errnoo = '未知错误 [ #'.$errno.' ]';  break;
        }
        return $errnoo;
    }
}