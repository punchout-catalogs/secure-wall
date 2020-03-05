<?php

namespace PunchoutCatalog\SecuredWall;

/**
 * Class Client
 *
 * @package PunchoutCatalog\SecuredWall
 */
class Client
{
    /** @var string */
    protected $secret;

    /** @var string */
    protected $token;

    /** @var string */
    protected $url;

    /** @var \Illuminate\Encryption\Encrypter */
    protected $encryter;

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var Client */
    static protected $instance = null;

    /**
     * @param string $secret
     * @param string $token
     * @param string $url
     
     * @return Client
     * @throws Exception
     */
    static public function getInstance($secret, $token, $url)
    {
        if (null === static::$instance) {
            static::validate($secret, $token, $url);
            static::$instance = new Client($secret, $token, $url);
        }
        return static::$instance;
    }

    /**
     * @param string $secret
     * @param string $token
     * @param string $url
     *
     * @return bool
     * @throws Exception
     */
    static protected function validate($secret, $token, $url)
    {
        if (empty($secret)) {
            throw new Exception('Empty Secret.', Exception::EMPTY_SECRET);
        }
        if (empty($token)) {
            throw new Exception('Empty Token.', Exception::EMPTY_TOKEN);
        }
        if (empty($url)) {
            throw new Exception('Empty Cloud URL.', Exception::EMPTY_CLOUD_URL);
        }
        return true;
    }

    /**
     * @param string $secret
     * @param string $token
     * @param string $url
     *
     * @param array $config
     */
    protected function __construct(string $secret, string $token, string $url)
    {
        $p = parse_url($url);
        
        if (empty($p['scheme'])) {
            $url = 'https://' . $url;
        }

        $this->secret = $secret;
        $this->token = $token;
        $this->url = rtrim($url, '/') . '/api/v2/wall/';

        $this->encryter = new \Illuminate\Encryption\Encrypter($secret, 'AES-256-CBC');

        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->url,
            'timeout'  => 2.0,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => "Bearer {$this->token}",
            ],
        ]);
    }

    /**
     * @param array|string $data
     * @param array|string $value
     *
     * @return bool
     * @throws Exception
     */
    public function set($data, $value = null)
    {
        if (is_array($data)) {
            $rows = $data;
        } else {
            $rows = [$data => $value];
        }

        return $this->write($rows);
    }
    
    /**
     * @param string $ids
     *
     * @return array|string
     * @throws Exception
     */
    public function get($ids)
    {
        if (empty($ids)) {
            throw new Exception('Empty Secured Wall ID.', Exception::EMPTY_ID);
        }
    
        $ids = is_array($ids) ? $ids : [$ids];
        $value = $this->read($ids);

        if (!is_array($value) || (count($value) != count($ids))) {
            throw new Exception('Some of the requested keys do not exists.', Exception::EMPTY_VALUE);
        }

        return $value;
    }
    
    /**
     * @param string $id
     *
     * @return array|string
     * @throws Exception
     */
    public function exists(string $id)
    {
        if ('' === $id) {
            throw new Exception('Empty Secured Wall ID.', Exception::EMPTY_ID);
        }
        
        $value = $this->read([$id]);

        return !empty($value);
    }

    protected function wrapResult($response)
    {
        $message = 'Could not save data in the secured wall.';

        $body = $response->getBody() ? @json_decode($response->getBody(), true) : [];
        if (!in_array($response->getStatusCode(), [200, 201])) {
            $message = isset($body['message']) ? $body['message'] : $message;
            throw new Exception($message, Exception::SAVE_ERROR);
        }
    
        $result = [];
        foreach ($body as $row) {
            if (isset($row['code']) && isset($row['value'])) {
                $row['value'] = $this->decode($row['value']);
                $result[$row['code']] = $row;
            }
        }
        
        return $result;
    }
    
    /**
     * @param array $rows
     *
     * @return bool
     */
    protected function write(array $rows)
    {
        $body = [];
        
        foreach ($rows as $code => $value) {
            if (empty($code)) {
                throw new Exception('Empty Secured Wall ID.', Exception::EMPTY_ID);
            }

            if (empty($value)) {
                throw new Exception('Empty Secured Wall Value.', Exception::EMPTY_VALUE_ENCODE);
            }

            $body[] = [
                "code" => $code,
                "value" => $value,
            ];
        }
        
        try {
            $response = $this->client->request('POST', '', ['json' => $body]);
        } catch (\Exception $e) {
            throw new Exception('Could not save data in the secured wall.', Exception::SAVE_ERROR);
        }

        return $this->wrapResult($response);
    }

    /**
     * @param array $ids
     *
     * @return string
     */
    protected function read(array $ids)
    {
        try {
            $response = $this->client->request('GET', '?code:in='. implode(',', $ids));
        } catch (\Exception $e) {
            throw new Exception('Could not read data from the secured wall.', Exception::SAVE_ERROR);
        }
    
        return $this->wrapResult($response);
    }
    
    protected function decode($value)
    {
        $value = $this->encryter->decrypt($value, false);
        return json_decode($value, true);
    }
}
