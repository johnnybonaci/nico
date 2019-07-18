<?php

class RestWs
{
    private $curl;
    private $mode;
    private $result;

    /**
     * RestWsIntegracionInc constructor.
     * @param $mode
     * @param $url
     * @param $headers
     */
    public function __construct( $url, $headers = array() )
    {
        // Inicialización del cURL
        $this->curl = curl_init($url);
        // Se establece para que NO retorne automaticamente el resultado
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FAILONERROR, true);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * @param string $mode
     */
    public function setMode( $mode )
    {
        $this->mode = $mode;
    }

    /**
     * @param array $headers
     */
    public function setHeaders( $headers )
    {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed|string
     */
    public function execute()
    {
        $this->result = curl_exec($this->curl);
        if (!curl_errno($this->curl)) {
            return curl_getinfo($this->curl);
        } else {
            return curl_error($this->curl);
        }
    }
}