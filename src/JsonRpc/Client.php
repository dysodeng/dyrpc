<?php
namespace DyRpc\JsonRpc;

class Client
{
    private $socket = null;

    /**
     * JsonRpcClient constructor.
     * @param $host
     * @param $port
     * @throws \Exception
     */
    public function __construct($host, $port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($this->socket, $host, $port) === false){
            socket_close($this->socket);
            throw new \Exception('create socket error', socket_last_error());
        }
    }

    /**
     * call rpc method
     * @param string $method
     * @param array $params
     * @return mixed|null
     * @throws \Exception
     */
    public function call($method, array $params = [])
    {
        $write = socket_write($this->socket, json_encode([
                'method' => $method,
                'params' => array($params),
                'id'     => 0,
            ])."\n");

        if($write === false){
            socket_close($this->socket);
            $message = sprintf("write socket error:%s", socket_strerror(socket_last_error()));
            throw new \Exception($message, socket_last_error());
        }

        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec'=>5, 'usec'=>0]);

        $rspBuffer = socket_read($this->socket, 65536);

        socket_close($this->socket);

        if($rspBuffer === false){
            return null;
        }

        return json_decode($rspBuffer, true);
    }

}