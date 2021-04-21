<?php

class Hit
{
    /**
     * @var array|false
     */
    protected $config;
    /**
     * @var mysqli
     */
    protected $mysqli;

    /**
     * Hit constructor.
     * @throws Exception
     */
    public function __construct($dbConfig)
    {
        $this->config = $dbConfig;
        $this->connectToDB();
    }

    /**
     * Make MySQL connection
     * @throws Exception
     */
    protected function connectToDB()
    {
        $this->mysqli =  new mysqli(
            $this->config['host'] ?? null,
            $this->config['username'] ?? null,
            $this->config['password'] ?? null,
            $this->config['dbname'] ?? null
        );

        if (mysqli_connect_errno()) {
            throw new Exception("Подключение не удалось: %s\n", mysqli_connect_error());
        }
    }

    /**
     * Add or increase hit
     * @param string $addr
     * @param string $useragent
     * @param string $url
     */
    public function addOrIncreaseHit(string $addr, string $useragent, string $url): void
    {
        $viewDate = date('Y-m-d');
        $hash = md5("$viewDate, $addr, $useragent, $url");

        if (!mysqli_query(
            $this->mysqli,
            'INSERT INTO hits (ip_address, user_agent, view_date, page_url, views_count, hash) '
            . 'VALUES('
            . 'INET_ATON("' . mysqli_real_escape_string($this->mysqli, $addr) . '"), '
            . '"' . mysqli_real_escape_string($this->mysqli, $useragent) . '", '
            . '"' . $viewDate . '", '
            . '"' . mysqli_real_escape_string($this->mysqli, $url) . '", '
            . '1, '
            . '"' . $hash . '") '
            . 'ON DUPLICATE KEY UPDATE views_count=views_count+1'
        )) {
            throw new Exception($this->mysqli->error);
        }
    }
}
