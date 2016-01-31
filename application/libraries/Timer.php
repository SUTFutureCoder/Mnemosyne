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
    private static $anonymousRuntimeStack = array();
    //匿名时间存放队列
    private static $anonymousRuntimeResultStack = array();

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
            self::$anonymousRuntimeStack[] = microtime(true);
        } else {
            self::$runtimeCollector[$key]['start'] = microtime(true);
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
            $tempKeys = array_keys(self::$anonymousRuntimeStack);
            $endKey = end($tempKeys);
            if (empty(self::$anonymousRuntimeStack)
                || false === $endKey
                || empty(self::$anonymousRuntimeStack[$endKey])){
                return false;
            }
            //塞入匿名结果栈
            self::$anonymousRuntimeResultStack[$endKey] = microtime(true) - self::$anonymousRuntimeStack[$endKey];
            unset(self::$anonymousRuntimeStack[$endKey]);
            return true;
        }

        if (empty(self::$runtimeCollector[$key])
                || empty(self::$runtimeCollector[$key]['start'])){
            return false;
        }

        self::$runtimeCollector[$key]['stop']  = microtime(true);
    }

    /**
     * 获取key的时间差，格式为s，ms，us
     *
     * @param string $key
     * @param string $type
     * @return bool|int
     */
    public static function get($key = null, $type = 's'){
        $type = strtolower($type);

        if (null === $key){
            $tempKeys = array_keys(self::$anonymousRuntimeResultStack);
            $endKey = current($tempKeys);
            if (empty(self::$anonymousRuntimeResultStack)
                || false === $endKey
                || empty(self::$anonymousRuntimeResultStack[$endKey])
                || !in_array($type, self::$timeType)){
                return false;
            }
            $floatUseTime = self::$anonymousRuntimeResultStack[$endKey];

            //出栈
            unset(self::$anonymousRuntimeResultStack[$endKey]);

        } else {
            if (empty(self::$runtimeCollector[$key])
                || empty(self::$runtimeCollector[$key]['start'])
                || empty(self::$runtimeCollector[$key]['stop'])
                || !in_array($type, self::$timeType)){
                return false;
            }

            $floatUseTime  = self::$runtimeCollector[$key]['stop'] - self::$runtimeCollector[$key]['start'];
        }



        switch ($type){
            case 's':
                return intval($floatUseTime);
            break;

            case 'ms':
                return intval($floatUseTime * 1000);
            break;

            case 'us':
                return intval($floatUseTime * 1000 * 1000);
            break;

            default:
                return false;
            break;
        }
    }

    public static function getTotal(){
        return self::$runtimeCollector + self::$anonymousRuntimeStack;
    }

}