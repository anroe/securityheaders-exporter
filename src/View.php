<?php

namespace App;

use Swoole\Http\Response;

class View
{
    private string $template =
        "# HELP securityheaders_grade Displays the returned securityheaders.com grade of the target host\n
        # TYPE securityheaders_grade gauge\n
        securityheaders_grade{grade='%s'} 1\n
        # HELP securityheaders_probe_success Displays whether the assessment succeeded or not\n
        # TYPE securityheaders_probe_success gauge\n
        securityheaders_probe_success %b\n
        # HELP securityheaders_probe_success System time in seconds since epoch (1970).\n
        # TYPE securityheaders_probe_success gauge\n
        securityheaders_time_seconds %b\n
        # HELP securityheaders_duration_seconds Displays how long the assessment took to complete in seconds.\n
        # TYPE securityheaders_duration_seconds gauge\n
        securityheaders_duration_seconds %b\n
        # HELP securityheaders_probe_cache Displays whether the assessment come from cache or not\n
        # TYPE securityheaders_probe_cache gauge\n
        securityheaders_probe_cache %s \n";

    public function render(): string
    {

    }

    public function renderError(Response $response, $statusCode = '400'): void
    {
        $response->status($statusCode);
        $response->end('500');
    }
}