<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogService
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;
    
    /**
     * @var object
     */
    protected $logData;

    /**
     * @var float|null
     */
    protected $startTime;
    
    /**
     * @var AgentService
     */
    protected $agentService;

    /**
     * LogService constructor.
     *
     * @param AgentService $agentService
     */
    public function __construct(AgentService $agentService)
    {
        $this->logData = (object)[
            'status' => 'success',
            'code' => 200,
            'level' => 'INFO',
            'task' => 'process_data',
            'message' => 'Data processing completed',
            'info'  => (object)[],
            'error' => (object)[],
            'duration_ms' => 0,
            'timestamp' => (object) [
                'utc' => Carbon::now()->utc()->toIso8601ZuluString(),
                'local_time' => Carbon::now()->setTimezone('Asia/Jakarta')->toIso8601String(),
            ],
            'version' => config('app.version'),
            'target' => config('app.target'),
            'service' => config('app.service'),
        ];

        $this->agentService = $agentService;
    }

    /**
     * Set status of the log.
     *
     * @param string $status
     * @return $this
     */
    public function status($status)
    {
        $this->logData->status = $status;
        return $this;
    }

    /**
     * Set log level.
     *
     * @param string $level
     * @return $this
     */
    public function level($level)
    {
        $this->logData->level = $level;
        return $this;
    }

    /**
     * Set HTTP response code.
     *
     * @param int $code
     * @return $this
     */
    public function code($code)
    {
        $this->logData->code = $code;
        return $this;
    }

    /**
     * Set log message.
     *
     * @param string $message
     * @return $this
     */
    public function message($message)
    {
        $this->logData->message = $message;
        return $this;
    }

    /**
     * Add additional request data.
     *
     * @param mixed $data
     * @return $this
     */
    public function additionalRequest($data)
    {
        $this->logData->info->additional_request = $data;
        if ($this->startTime === null) {
            $this->startTime = microtime(true);
        }
        return $this;
    }

    /**
     * Start logging process time.
     */
    public function start()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Attach request details to the log.
     *
     * @param \Illuminate\Http\Request $request
     * @return $this
     */
    public function request(Request $request)
    {
        $this->request = $request;
        $this->startTime = microtime(true);
        $this->logData->info->url = $this->request->url();
        $this->logData->info->method = $this->request->method();
        $this->logData->info->ip = $this->request->ip();
        $this->logData->info->request = $this->request->all();

        if ($user = Auth::user()) {
            $this->logData->info->id = $user->id;
            $this->logData->info->email = $user->email;
        }

        return $this;
    }

    /**
     * Set task name for logging.
     *
     * @param string $task
     * @return $this
     */
    public function task($task)
    {
        $this->logData->task = $task;
        return $this;
    }

    /**
     * Set API endpoint.
     *
     * @param string $endpoint
     * @return $this
     */
    public function endpoint($endpoint)
    {
        $this->logData->info->endpoint = $endpoint;
        return $this;
    }

    /**
     * Attach response details to the log.
     *
     * @param mixed $response
     * @return $this
     */
    public function response($response)
    {
        $this->logData->info->response = $response;
        return $this;
    }

    /**
     * Log an error.
     *
     * @param \Throwable $error
     * @return $this
     */
    public function error(\Throwable $error)
    {
        $this->logData->error = $error;
        return $this;
    }

    /**
     * Save the log data.
     *
     * @return bool
     */
    public function save()
    {
        $this->logData->duration_ms = $this->getDuration();

        // Use request from the object if set, otherwise try to get the current request
        $requestToUse = $this->request ?? request();

        // Ensure we have a valid request object
        $this->logData->info->agent = $this->agentService->getAgent(
            $requestToUse instanceof Request ? $requestToUse : null
        );
        
        Log::channel('activity')->log($this->logData->level, $this->logData->message, (array)$this->logData);
        return true;
    }

    /**
     * Calculate request duration.
     *
     * @return int
     */
    protected function getDuration()
    {
        return (int) round((microtime(true) - $this->startTime) * 1000, 0);
    }
}
