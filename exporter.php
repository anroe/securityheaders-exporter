<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

use App\SecurityHeaders;
use App\View;

require __DIR__ . '/vendor/autoload.php';

$server = new Server("0.0.0.0", 9501);

$server->on("start", function (Server $server) {
    echo "Http server is started at http://0.0.0.0:9501\n";
});


$server->on("request", function (Request $request, Response $response) {
    $response->header("Content-Type", "text/plain");

    try {
        $securityHeaders = new SecurityHeaders($request->get['target']);
    } catch (\Exception $e) {
        \swoole_error_log(SWOOLE_LOG_ERROR, $e);
        (new View)->renderError($response);
        return;
    }

    var_dump($securityHeaders->target);

    $cacheKey = '/' . md5($securityHeaders->target) . '.cache.html';
    $cachePathFile = sys_get_temp_dir() . $cacheKey;
    if (!file_exists($cachePathFile) || filemtime($cachePathFile) + 604800 < time()) {
        // Used to calculate securityheaders_duration_seconds metric
        $startTimestamp = time();

        $grade = new $securityHeaders->getGrade();
        die;

        $data =  '# HELP securityheaders_grade Displays the returned securityheaders.com grade of the target host' . "\n";
        $data .= '# TYPE securityheaders_grade gauge' . "\n";
        $data .= 'securityheaders_grade{grade="' . $grade . '"} 1' . "\n";
        $data .= '# HELP securityheaders_probe_success Displays whether the assessment succeeded or not' . "\n";
        $data .= '# TYPE securityheaders_probe_success gauge' . "\n";
        $data .= 'securityheaders_probe_success 1' . "\n";
        $data .= '# HELP securityheaders_probe_success System time in seconds since epoch (1970).' . "\n";
        $data .= '# TYPE securityheaders_probe_success gauge' . "\n";
        $data .= 'securityheaders_time_seconds ' . time() . "\n";
        $data .= '# HELP securityheaders_duration_seconds Displays how long the assessment took to complete in seconds.' . "\n";
        $data .= '# TYPE securityheaders_duration_seconds gauge' . "\n";
        $data .= 'securityheaders_duration_seconds ' . (time() - $startTimestamp) . "\n";

        // Create cache
        if (file_exists($cachePathFile)) {
            unlink($cachePathFile);
        }
        file_put_contents($cachePathFile, $data);
        $fromCache = '0';
    } else {
        $data = file_get_contents($cachePathFile);
        $fromCache = '1';
    }

    $data .= '# HELP securityheaders_probe_cache Displays whether the assessment come from cache or not' . "\n";
    $data .= '# TYPE securityheaders_probe_cache gauge' . "\n";
    $data .= 'securityheaders_probe_cache ' . $fromCache . "\n";

    $response->end($data);
});


$server->start();




