<?php

namespace App\Services;

use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;

class AgentService
{
    /**
     * @var Agent
     */
    protected $agent;

    /**
     * AgentService constructor.
     * Initializes the Agent instance.
     */
    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Retrieve agent details from the request.
     *
     * @param Request $request
     * @return array{
     *     browser: string,
     *     browser_version: string,
     *     platform: string,
     *     platform_version: string,
     *     device: string,
     *     is_mobile: bool,
     *     is_desktop: bool,
     *     user_agent: string
     * }
     */
    public function getAgent(?Request $request = null): array
    {
        if ($request === null) {
            return [
                'browser' => 'N/A',
                'browser_version' => 'N/A',
                'platform' => 'console',
                'platform_version' => 'N/A',
                'device' => 'server',
                'is_mobile' => false,
                'is_desktop' => false,
                'user_agent' => 'Console/CLI'
            ];
        }

        return [
            'browser' => $this->agent->browser(),
            'browser_version' => $this->agent->version($this->agent->browser()),
            'platform' => $this->agent->platform(),
            'platform_version' => $this->agent->version($this->agent->platform()),
            'device' => $this->agent->device(),
            'is_mobile' => $this->agent->isMobile(),
            'is_desktop' => $this->agent->isDesktop(),
            'user_agent' => $this->agent->getUserAgent()
        ];
    }
}
