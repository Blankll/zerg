<?php
namespace app\lib\exception;

use think\exception\Handle;
use app\lib\exception\BaseException;
use think\Request;
use think\Log;

class ExceptionHandler extends Handle {
    private $code;
    private $message;
    private $error_code;
    private $request_url;

    public function render(\Exception $e)
    {
        $this->request_url = Request::instance()->url();
        if($e instanceof BaseException)
        {
            //自定义异常
            $this->code = $e->code;
            $this->message = $e->message;
            $this->error_code = $e->error_code;
        }else
        {
            //系统异常
            //返回原框架异常
            if(\think\Env::get('EXCEPTION_ORIGNAL')) return parent::render($e);
            //返回自定义异常
            $this->code = 500;
            $this->message = '系统内部错误';
            $this->error_code = 999;
            $this->recordErrorLog($e);
        }
        $result = [
            'error_code' => $this->error_code,
            'message' => $this->message,
            'request_url' =>$this->request_url
        ];

        return json($result,$this->code);
    }

    private function recordErrorLog(\Exception $e)
    {
        //初始化日志
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(),'error');
    }
}
