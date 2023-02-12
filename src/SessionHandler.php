<?php

/**
 * Session Handler Class
 *
 * @author Ramazan Çetinkaya
 * @date 2023-02-12
 */
namespace SessionHandler;

class SessionHandler
{
    /**
     * @var string
     */
    private $savePath;

    /**
     * @var int
     */
    private $sessionLifetime;

    /**
     * @var string
     */
    private $sessionName;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * SessionHandler constructor.
     *
     * @param string $savePath
     * @param int $sessionLifetime
     * @param string $sessionName
     * @param string $secretKey
     */
    public function __construct(
        string $savePath,
        int $sessionLifetime,
        string $sessionName,
        string $secretKey
    ) {
        $this->savePath = $savePath;
        $this->sessionLifetime = $sessionLifetime;
        $this->sessionName = $sessionName;
        $this->secretKey = $secretKey;

        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroy'],
            [$this, 'gc']
        );
        session_name($this->sessionName);
        session_set_cookie_params($this->sessionLifetime);
    }

    /**
     * Open session.
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open(string $savePath, string $sessionName): bool
    {
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0700);
        }

        return true;
    }

    /**
     * Close session.
     *
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * Read session.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public function read(string $sessionId): string
    {
        $sessionFile = $this->savePath . '/' . $sessionId;
        if (!file_exists($sessionFile)) {
            return '';
        }

        $encryptedData = file_get_contents($sessionFile);
        $iv = substr($encryptedData, 0, 16);
        $encryptedData = substr($encryptedData, 16);
        $data = openssl_decrypt($encryptedData, 'AES-256-CBC', $this->secretKey, 0, $iv);
        return $data;
    }

    /**
     * Write session.
     *
     * @param string $sessionId
     * @param string $data
     *
     * @return bool
     */
    public function write(string $sessionId, string $data): bool
    {
        $iv = random_bytes(16);
        $encryptedData = openssl_encrypt($data, 'AES-256-CBC', $this->secretKey, 0, $iv);
        $encryptedData = $iv . $encryptedData;

        $sessionFile = $this->savePath . '/' . $sessionId;
        return (bool) file_put_contents($sessionFile, $encryptedData);
    }

    /**
     * Destroy session.
     *
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy(string $sessionId): bool
    {
        $sessionFile = $this->savePath . '/' . $sessionId;
        if (file_exists($sessionFile)) {
            unlink($sessionFile);
        }

        return true;
    }

    /**
     * Garbage collector.
     *
     * @param int $maxLifetime
     *
     * @return bool
     */
    public function gc(int $maxLifetime): bool
    {
        $files = glob($this->savePath . '/*');
        $now = time();

        foreach ($files as $file) {
            if (filemtime($file) + $this->sessionLifetime < $now) {
                unlink($file);
            }
        }

        return true;
    }
}
