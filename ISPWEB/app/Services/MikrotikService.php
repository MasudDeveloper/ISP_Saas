<?php

namespace App\Services;

use App\Models\MikrotikRouter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    protected $api;
    protected $router;
    protected $connected = false;

    public function __construct(MikrotikRouter $router)
    {
        $this->router = $router;
        $this->api = new RouterosAPI();
        // $this->api->debug = env('APP_DEBUG', false);
        $this->api->port = $router->api_port;
        
        $this->connect();
    }

    /**
     * Connect to the RouterOS instance
     */
    protected function connect()
    {
        try {
            $password = Crypt::decryptString($this->router->password);
        } catch (\Exception $e) {
            // Fallback for testing if not encrypted properly yet
            $password = $this->router->password;
        }

        if ($this->api->connect($this->router->host, $this->router->username, $password)) {
            $this->connected = true;
        } else {
            Log::error("Mikrotik Connection Failed for Router: {$this->router->name}");
            throw new \Exception("Could not connect to Mikrotik Router: {$this->router->name}");
        }
    }

    /**
     * Check connection status
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Enable a PPPoE secret
     */
    public function enablePppSecret($username)
    {
        $secrets = $this->api->comm('/ppp/secret/print', ['?name' => $username]);
        if (!empty($secrets) && !isset($secrets['error'])) {
            $id = $secrets[0]['.id'];
            $this->api->comm('/ppp/secret/enable', ['.id' => $id]);
            return true;
        }
        return false;
    }

    /**
     * Disable a PPPoE secret
     */
    public function disablePppSecret($username)
    {
        $secrets = $this->api->comm('/ppp/secret/print', ['?name' => $username]);
        if (!empty($secrets) && !isset($secrets['error'])) {
            $id = $secrets[0]['.id'];
            $this->api->comm('/ppp/secret/disable', ['.id' => $id]);
            return true;
        }
        return false;
    }

    /**
     * Disconnect active PPPoE session
     */
    public function disconnectActiveSession($username)
    {
        $activeConnections = $this->api->comm('/ppp/active/print', ['?name' => $username]);
        if (!empty($activeConnections) && !isset($activeConnections['error'])) {
            foreach ($activeConnections as $conn) {
                $this->api->comm('/ppp/active/remove', ['.id' => $conn['.id']]);
            }
            return true;
        }
        return false;
    }

    /**
     * Change the profile of a PPPoE secret
     */
    public function changeProfile($username, $profileName)
    {
        $secrets = $this->api->comm('/ppp/secret/print', ['?name' => $username]);
        if (!empty($secrets) && !isset($secrets['error'])) {
            $id = $secrets[0]['.id'];
            $this->api->comm('/ppp/secret/set', [
                '.id' => $id,
                'profile' => $profileName
            ]);
            return true;
        }
        return false;
    }

    /**
     * Add or Update Simple Queue for IP Binding
     */
    public function setSimpleQueue($name, $targetIp, $maxLimit)
    {
        // $maxLimit format: "10M/10M" (Upload/Download)
        $existing = $this->api->comm('/queue/simple/print', ['?name' => $name]);
        
        if (!empty($existing) && !isset($existing['error'])) {
            // Update
            $this->api->comm('/queue/simple/set', [
                '.id' => $existing[0]['.id'],
                'target' => $targetIp,
                'max-limit' => $maxLimit
            ]);
        } else {
            // Add new
            $this->api->comm('/queue/simple/add', [
                'name' => $name,
                'target' => $targetIp,
                'max-limit' => $maxLimit
            ]);
        }
        return true;
    }

    /**
     * Get Router CPU and Memory Usage
     */
    public function getRouterResource()
    {
        $resource = $this->api->comm('/system/resource/print');
        if (!empty($resource) && !isset($resource['error'])) {
            return $resource[0];
        }
        return null;
    }

    public function __destruct()
    {
        if ($this->connected) {
            $this->api->disconnect();
        }
    }
}
