<?php

namespace Bakcay\DailyMotion;

use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;

class DailyMotion {
    const ROOT_ENDPOINT        = 'https://api.dailymotion.com';
    const OAUTH_TOKEN_ENDPOINT = 'https://api.dailymotion.com/oauth/token';

    private $config;
    private $client;
    private $options;
    private $video_url = false;


    public function __construct() {
        $this->config = config('dailymotion');
        $this->client = new Client();
    }

    /**
     * @param $url
     *
     * @return DailyMotion
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public function file($url = null) {
        // Get File Upload Url
        $upload_url                 = ($this->get('/file/upload'))->upload_url;
        $this->options['multipart'] = [
            [
                'name'     => 'file',
                'contents' => fopen($url, 'r'),
            ],
        ];
        $response                   = $this->call('POST', $upload_url, $this->options);
        $this->video_url            = $response->url;
        unset($this->options['multipart']);

        return $this;
    }

    /**
     * @param $path
     * @param bool $args
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public function get($path, $args = false) {
        if ($args) {
            $this->options['query'] = $args;
        }

        return $this->call('GET', $this->getEndPoint($path), $this->options);
    }

    /**
     * @param $path
     * @param bool $args
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public function post($path, $args = false) {
        if ($args) {
            if ($this->video_url) {
                $args['url'] = $this->video_url;
            }
            $this->options['form_params'] = $args;
        }

        return $this->call('POST', $this->getEndPoint($path), $this->options);
    }

    /**
     * @param $path
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public function delete($path) {
        return $this->call('DELETE', $this->getEndPoint($path), $this->options);
    }

    /**
     * @param $method
     * @param $path
     * @param array $options
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    private function call($method, $path, $options = []) {
        $response = $this->client->request($method, $path, $options);

        return json_decode($response->getBody()
                                    ->getContents());
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    private function requestAccessToken() {
        $data = $this->call('POST', self::OAUTH_TOKEN_ENDPOINT, [
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => $this->config->get('dailymotion.client_id'),
                'client_secret' => $this->config->get('dailymotion.client_secret'),
                'username'      => $this->config->get('dailymotion.username'),
                'password'      => $this->config->get('dailymotion.password'),
                'scope'         => $this->config->get('dailymotion.scope'),
            ],
        ]);

        return $data->access_token;
    }

    /**
     * @return mixed
     * @throws \Exception
     *
     */
    public function getAccessToken() {
        return \Bakcay\DailyMotion\Services\cache()->remember('dailymotion_token', 60, function () {
            return $this->requestAccessToken();
        });
    }

    /**
     * @param $path
     *
     * @return string
     */
    private function getEndPoint($path): string {
        return self::ROOT_ENDPOINT . "/{$path}";
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options): void {
        $this->options = $options;
    }
}
