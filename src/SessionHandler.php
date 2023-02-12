<?php


class SessionHandler implements \SessionHandlerInterface
{
    
    // Save path for the session files
    private string $savePath;
    // Use a secure encryption algorithm and a strong encryption key
    private string $encryptionMethod = 'AES-256-CBC';
    private string $encryptionKey;
    // Check client's IP address and user agent
    private string $clientIp;
    private string $clientUserAgent;
    // Regenerate the session ID every 30 minutes
    private int $regenerateInterval = 1800;
    private int $lastRegeneration = 0;

    public function __construct(string $savePath, string $encryptionKey, string $clientIp, string $clientUserAgent)
    {
        $this->savePath = $savePath;
        $this->encryptionKey = $encryptionKey;
        $this->clientIp = $clientIp;
        $this->clientUserAgent = $clientUserAgent;
    }

    public function open($savePath, $sessionName): bool
    {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0700);
        }
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $sessionId): string
    {
        if ($_SERVER['REMOTE_ADDR'] !== $this->clientIp || $_SERVER['HTTP_USER_AGENT'] !== $this->clientUserAgent) {
            // destroy the session and return an empty string
            $this->destroy($sessionId);
            return '';
        }
        $encryptedData = (string) file_get_contents("$this->savePath/sess_$sessionId");
        return openssl_decrypt($encryptedData, $this->encryptionMethod, $this->encryptionKey);
    }

    public function write(string $sessionId, string $sessionData): bool
    {
        if (time() - $this->lastRegeneration > $this->regenerateInterval) {
            session_regenerate_id(true);
            $this->lastRegeneration = time();
        }
        $encryptedData = openssl_encrypt($sessionData, $this->encryptionMethod, $this->encryptionKey);
        return (bool) file_put_contents("$this->savePath/sess_$sessionId", $encryptedData);
    }

    public function regenerateAfterLogin(): void
    {
        session_regenerate_id(true);
    }

    public function destroy(string $sessionId): bool
    {
        $file = "$this->savePath/sess_$sessionId";
        if (file_exists($file)) {
            unlink($file);
        }
        return true;
    }

    public function gc(int $maxlifetime): bool
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }
        return true;
    }
  
}
