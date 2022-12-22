<?php

/**
 * @internal
 */
class SageParsersMicrotime extends SageParser
{
    private static $_times = array();
    private static $_laps = array();

    protected static function parse(&$variable, $varData)
    {
        if (! SageHelper::isRichMode()
            || ! is_string($variable)
            || ! preg_match('/^0\.[\d]{8} [\d]{10}$/', $variable)) {
            return false;
        }

        list($usec, $sec) = explode(" ", $variable);

        $time = (float)$usec + (float)$sec;
        if (SageHelper::php53()) {
            $size = memory_get_usage(true);
        }

        // '@' is used to prevent the dreaded timezone not set error
        $result = @date('Y-m-d H:i:s', $sec).'.'.substr($usec, 2, 4);

        $numberOfCalls = count(self::$_times);
        if ($numberOfCalls > 0) { // meh, faster than count($times) > 1
            $lap = $time - end(self::$_times);
            self::$_laps[] = $lap;

            // todo allow in plain text views too
            $result .= "\n<b>SINCE LAST CALL:</b> <b class=\"_sage-microtime\">".round($lap, 4).'</b>s.';
            if ($numberOfCalls > 1) {
                $result .= "\n<b>SINCE START:</b> ".round($time - self::$_times[0], 4).'s.';
                $result .= "\n<b>AVERAGE DURATION:</b> "
                    .round(array_sum(self::$_laps) / $numberOfCalls, 4).'s.';
            }
        }

        $unit = array('B', 'KB', 'MB', 'GB', 'TB');
        if (SageHelper::php53()) {
            $result .= "\n<b>MEMORY USAGE:</b> ".$size." bytes ("
                .round($size / pow(1024, ($i = floor(log($size, 1024)))), 3).' '.$unit[$i].")";
        }

        self::$_times[] = $time;

        $varData->addTabToView('Benchmark', $result);
    }

    /*
    function test() {
        d( 'start', microtime() );
        for ( $i = 0; $i < 10; $i++ ) {
            d(
                $duration = mt_rand( 0, 200000 ), // the reported duration will be larger because of Sage overhead
                usleep( $duration ),
                microtime()
            );
        }
        dd(  );
    }
     */
}