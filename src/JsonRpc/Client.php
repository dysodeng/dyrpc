<?php
namespace DyRpc\JsonRpc;

class Client
{
    private $conn = null;

    /**
     * JsonRpcClient constructor.
     * @param $host
     * @param $port
     * @throws \Exception
     */
    public function __construct($host, $port)
    {
        $this->conn = fsockopen($host, $port, $errno, $error, 3);
        if (!$this->conn) {
            throw new \Exception('connect socket error: '.$error, $errno);
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
        $error = fwrite($this->conn, json_encode([
                'method' => $method,
                'params' => array($params),
                'id'     => 0,
            ])."\n");
        if ($error === false) {
            return null;
        }

        stream_set_timeout($this->conn, 5);
        $rspBuffer = fgets($this->conn);

        if($rspBuffer === false){
            return null;
        }

        return json_decode($rspBuffer, true);
    }

}