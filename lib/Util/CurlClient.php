<?php

namespace Minicli\Util;

class CurlClient
{
    protected $last_response;

    /**
     * Makes a GET query
     * @param string $endpoint API endpoint
     * @param array $headers optional headers
     * @return mixed
     */
    public function get($endpoint, array $headers = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $endpoint,
            CURLINFO_HEADER_OUT => true
        ]);

        return $this->getQueryResponse($curl);
    }

    /**
     * Makes a POST query
     * @param $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     */
    public function post($endpoint, array $params, $headers = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_URL => $endpoint,
            #CURLINFO_HEADER_OUT => true,
            CURLOPT_TIMEOUT => 120,
        ]);

        return $this->getQueryResponse($curl);
    }

    /**
     * Makes a DELETE query
     * @param $endpoint
     * @param array $headers
     * @return array
     */
    public function delete($endpoint, $headers = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_URL => $endpoint,
            #CURLINFO_HEADER_OUT => true,
        ]);

        return $this->getQueryResponse($curl);
    }

    /**
     * Exec curl and get response
     * @param $curl
     * @return array
     */
    protected function getQueryResponse($curl)
    {
        $response = curl_exec($curl);
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [ 'code' => $response_code, 'body' => $response ];
    }
}