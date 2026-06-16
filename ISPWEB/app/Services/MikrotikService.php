<?php

namespace App\Services;

use App\Models\MikrotikRouter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    protected $client;
    protected $router;
    protected $connected = false;

    public function __construct(MikrotikRouter $router)
    {
        $this->router = $router;
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

        try {
            $this->client = new Client([
                'host' => $this->router->host,
                'user' => $this->router->username,
                'pass' => $password,
                'port' => (int) $this->router->api_port
            ]);
            $this->connected = true;
        } catch (\Exception $e) {
            Log::error("Mikrotik Connection Failed for Router: {$this->router->name} - " . $e->getMessage());
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
        if (!$this->connected) return false;

        $query = (new Query('/ppp/secret/print'))->where('name', $username);
        $secrets = $this->client->query($query)->read();

        if (!empty($secrets)) {
            $id = $secrets[0]['.id'];
            $updateQuery = (new Query('/ppp/secret/set'))
                ->equal('.id', $id)
                ->equal('disabled', 'no');
            $this->client->query($updateQuery)->read();
            return true;
        }
        return false;
    }

    /**
     * Disable a PPPoE secret
     */
    public function disablePppSecret($username)
    {
        if (!$this->connected) return false;

        $query = (new Query('/ppp/secret/print'))->where('name', $username);
        $secrets = $this->client->query($query)->read();

        if (!empty($secrets)) {
            $id = $secrets[0]['.id'];
            $updateQuery = (new Query('/ppp/secret/set'))
                ->equal('.id', $id)
                ->equal('disabled', 'yes');
            $this->client->query($updateQuery)->read();
            return true;
        }
        return false;
    }

    /**
     * Disconnect active PPPoE session
     */
    public function disconnectActiveSession($username)
    {
        if (!$this->connected) return false;

        $query = (new Query('/ppp/active/print'))->where('name', $username);
        $activeConnections = $this->client->query($query)->read();

        if (!empty($activeConnections)) {
            foreach ($activeConnections as $conn) {
                $removeQuery = (new Query('/ppp/active/remove'))->equal('.id', $conn['.id']);
                $this->client->query($removeQuery)->read();
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
        if (!$this->connected) return false;

        $query = (new Query('/ppp/secret/print'))->where('name', $username);
        $secrets = $this->client->query($query)->read();

        if (!empty($secrets)) {
            $id = $secrets[0]['.id'];
            $updateQuery = (new Query('/ppp/secret/set'))
                ->equal('.id', $id)
                ->equal('profile', $profileName);
            $this->client->query($updateQuery)->read();
            return true;
        }
        return false;
    }

    /**
     * Add or Update Simple Queue for IP Binding
     */
    public function setSimpleQueue($name, $targetIp, $maxLimit)
    {
        if (!$this->connected) return false;

        $query = (new Query('/queue/simple/print'))->where('name', $name);
        $existing = $this->client->query($query)->read();
        
        if (!empty($existing)) {
            // Update
            $updateQuery = (new Query('/queue/simple/set'))
                ->equal('.id', $existing[0]['.id'])
                ->equal('target', $targetIp)
                ->equal('max-limit', $maxLimit);
            $this->client->query($updateQuery)->read();
        } else {
            // Add new
            $addQuery = (new Query('/queue/simple/add'))
                ->equal('name', $name)
                ->equal('target', $targetIp)
                ->equal('max-limit', $maxLimit);
            $this->client->query($addQuery)->read();
        }
        return true;
    }

    /**
     * Get Router CPU and Memory Usage
     */
    public function getRouterResource()
    {
        if (!$this->connected) return null;

        $query = new Query('/system/resource/print');
        $resource = $this->client->query($query)->read();

        if (!empty($resource)) {
            return $resource[0];
        }
        return null;
    }
}
