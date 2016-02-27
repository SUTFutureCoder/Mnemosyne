<?php
/**
 * 计时器
 *
 * 一般用于请求总执行时间、数据库指令执行时间、debug打断点测试的记录
 *
 * Created by PhpStorm.
 * User: lin
 * Date: 16-1-31
 * Time: 下午5:51
 */
class Timer{

    private static $timeType = array(
        's', 'ms', 'us',
    );

    private static $runtimeCollector = array();

    //匿名时间存放栈,用于存放没有名字key的情况
    private static $anonymousRuntimeStack = null;
    //匿名时间存放队列
    private static $anonymousRuntimeResultQueue = null;

    /**
     * 开始针对key计时，如果不指定key则会自动使用匿名存放栈.
     *
     * 推荐key设置为模块或要测试的模块名称
     *
     * key对于多层嵌套的结构非常有用 :-)
     *
     * @param string $key
     */
    public static function start($key = null){
        //请用key来区分计时器作用
        if (null === $key){
            //使用匿名存放栈
            if (null === self::$anonymousRuntimeStack){
                self::$anonymousRuntimeStack = new SplStack();
            }
            self::$anonymousRuntimeStack->push(microtime(true));
        } else {
            self::$runtimeCollector[$key][0] = microtime(true);
        }
    }

    /**
     * 结束针对key计时
     *
     * @param string $key
     * @return bool
     */
    public static function stop($key = null){
        if (null === $key){
            if (null === self::$anonymousRuntimeStack
                || true === self::$anonymousRuntimeStack->isEmpty()){
                MLog::fatal(CoreConst::MODULE_KERNEL, 'stop anonymous timer error when stack is empty');
                return false;
            }

            //塞入匿名结果栈
            if (null === self::$anonymousRuntimeResultQueue){
                self::$anonymousRuntimeResultQueue = new SplQueue();
            }

            self::$anonymousRuntimeResultQueue->enqueue(microtime(true) - self::$anonymousRuntimeStack->pop());
            return true;
        }

        if (empty(self::$runtimeCollector[$key])
                || empty(self::$runtimeCollector[$key][0])){
            MLog::fatal(CoreConst::MODULE_KERNEL, 'timer failed the run time collector is empty when stop timer');
            return false;
        }

        self::$runtimeCollector[$key][1]  = microtime(true);
    }

    /**
     * 获取key的时间差，格式为s，ms，us
     *
     * @param string $key
     * @param string $type
     * @return bool|int
     */
    public static function get($key = null, $type = 'ms'){
        $type = strtolower($type);

        if (null === $key){
            if (null === self::$anonymousRuntimeResultQueue
                || true === self::$anonymousRuntimeResultQueue->isEmpty()){
                MLog::fatal(CoreConst::MODULE_KERNEL, 'get anonymous timer error when runtime queue is empty');
                return false;
            }

            $floatUseTime  = self::$anonymousRuntimeResultQueue->dequeue();

        } else {
            if (empty(self::$runtimeCollector[$key])
                || empty(self::$runtimeCollector[$key][0])
                || empty(self::$runtimeCollector[$key][1])
                || !in_array($type, self::$timeType)){
                MLog::fatal(CoreConst::MODULE_KERNEL, sprintf('get timer error when runtime queue is empty key[%s]', $key));
                return false;
            }

            $floatUseTime  = self::$runtimeCollector[$key][1] - self::$runtimeCollector[$key][0];
        }



        switch ($type){
            case 's':
                return intval($floatUseTime);
            break;

            case 'ms':
                return $floatUseTime * 1000;
            break;

            case 'us':
                return $floatUseTime * 1000 * 1000;
            break;

            default:
                MLog::fatal(CoreConst::MODULE_KERNEL, 'timer type error');
                return false;
            break;
        }
    }

}