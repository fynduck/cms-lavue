<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array
     */
    public function hosts()
    {
        return [
            'md021.punct.org',
            'md021.punct.org:81',
            'md021.punct.org:82',
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
