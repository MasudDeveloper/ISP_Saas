<?php

namespace App\Services;

/**
 * RouterOS API client class.
 * Standard fsockopen based wrapper for communicating with MikroTik RouterOS.
 */
class RouterosAPI
{
    public $debug = false;
    public $connected = false;
    public $port = 8728;
    public $timeout = 3;
    public $attempts = 5;
    public $delay = 3;
    
    private $socket;
    private $error_no;
    private $error_str;

    /**
     * Connect to RouterOS
     */
    public function connect($ip, $login, $password)
    {
        for ($ATTEMPT = 1; $ATTEMPT <= $this->attempts; $ATTEMPT++) {
            $this->connected = false;
            $this->socket = @fsockopen($ip, $this->port, $this->error_no, $this->error_str, $this->timeout);
            if ($this->socket) {
                socket_set_timeout($this->socket, $this->timeout);
                $this->write('/login', false);
                $this->write('=name=' . $login, false);
                $this->write('=password=' . $password);
                $RESPONSE = $this->read(false);
                if (isset($RESPONSE[0]) && $RESPONSE[0] == '!done') {
                    $this->connected = true;
                    break;
                } else {
                    // Try older challenge-response (pre RouterOS v6.43)
                    if (isset($RESPONSE[1])) {
                        $MATCHES = [];
                        if (preg_match('/^=ret=([0-9a-fA-F]+)$/', $RESPONSE[1], $MATCHES)) {
                            $challenge = pack('H*', $MATCHES[1]);
                            $md5 = md5("\x00" . $password . $challenge);
                            $this->write('/login', false);
                            $this->write('=name=' . $login, false);
                            $this->write('=response=00' . $md5);
                            $RESPONSE = $this->read(false);
                            if (isset($RESPONSE[0]) && $RESPONSE[0] == '!done') {
                                $this->connected = true;
                                break;
                            }
                        }
                    }
                }
                fclose($this->socket);
            }
            sleep($this->delay);
        }
        return $this->connected;
    }

    /**
     * Disconnect from RouterOS
     */
    public function disconnect()
    {
        if ($this->socket) {
            fclose($this->socket);
        }
        $this->connected = false;
    }

    /**
     * Encode length
     */
    private function encodeLength($length)
    {
        if ($length < 0x80) {
            $length = chr($length);
        } elseif ($length < 0x4000) {
            $length |= 0x8000;
            $length = chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        } elseif ($length < 0x200000) {
            $length |= 0xC00000;
            $length = chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        } elseif ($length < 0x10000000) {
            $length |= 0xE0000000;
            $length = chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        } elseif ($length >= 0x10000000) {
            $length = chr(0xF0) . chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        }
        return $length;
    }

    /**
     * Write command
     */
    public function write($command, $param2 = true)
    {
        if ($command) {
            $data = explode("\n", $command);
            foreach ($data as $com) {
                $com = trim($com);
                fwrite($this->socket, $this->encodeLength(strlen($com)) . $com);
            }
            if ($param2) {
                fwrite($this->socket, chr(0));
            }
        }
    }

    /**
     * Read response
     */
    public function read($parse = true)
    {
        $RESPONSE = [];
        $receiveddone = false;
        while (true) {
            // Read the first byte of input which gives us some or all of the length
            $BYTE = ord(fread($this->socket, 1));
            $length = 0;
            // If the first bit is set then we need to remove the first four bits
            if ($BYTE & 128) {
                if (($BYTE & 192) == 128) {
                    $length = (($BYTE & 63) << 8) + ord(fread($this->socket, 1));
                } else {
                    if (($BYTE & 224) == 192) {
                        $length = (($BYTE & 31) << 8) + ord(fread($this->socket, 1));
                        $length = ($length << 8) + ord(fread($this->socket, 1));
                    } else {
                        if (($BYTE & 240) == 224) {
                            $length = (($BYTE & 15) << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                        } else {
                            $length = ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                        }
                    }
                }
            } else {
                $length = $BYTE;
            }

            if ($length > 0) {
                $_ = "";
                $retlen = 0;
                while ($retlen < $length) {
                    $toread = $length - $retlen;
                    $_ .= fread($this->socket, $toread);
                    $retlen = strlen($_);
                }
                $RESPONSE[] = $_;
            }

            if ($_ == "!done") {
                $receiveddone = true;
            }
            
            $STATUS = socket_get_status($this->socket);
            if ($length > 0) {
                $dummystatus = $STATUS["unread_bytes"];
            } else {
                if ($STATUS["unread_bytes"] == 0 && $receiveddone) {
                    break;
                }
            }
        }

        if ($parse) {
            return $this->parseResponse($RESPONSE);
        }
        
        return $RESPONSE;
    }

    /**
     * Parse response into associative array
     */
    public function parseResponse($response)
    {
        $result = [];
        $i = -1;
        foreach ($response as $line) {
            if ($line == '!re') {
                $i++;
            } elseif ($line == '!trap') {
                return ['error' => true, 'data' => $response];
            } else {
                $matches = [];
                if (preg_match('/^=([^=]+)=(.*)$/', $line, $matches)) {
                    $result[$i][$matches[1]] = $matches[2];
                }
            }
        }
        return $result;
    }

    /**
     * Helper to run commcommands cleanly
     */
    public function comm($com, $arr = [])
    {
        $this->write($com, empty($arr));
        $i = 0;
        $count = count($arr);
        foreach ($arr as $k => $v) {
            $i++;
            $this->write('=' . $k . '=' . $v, ($i == $count));
        }
        return $this->read();
    }
}
