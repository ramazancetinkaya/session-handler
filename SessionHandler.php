<?php
/**
 * SessionHandler
 *
 * Copyright (c) [2023] [Ramazan Çetinkaya]
 * 
 * @author [ramazancetinkaya] (https://github.com/ramazancetinkaya)
 * @date [28.01.2023]
 *
 * Software Ownership and Attribution
 *
 * As the author of this software, I retain ownership of the code and any associated intellectual property. However, I am happy to make the software available for use in open source projects, under the following conditions:
 *
 * 1. Attribution: Any use of the software in a public or open source project must clearly indicate my name as the original author of the code. This includes, but is not limited to, including my name in the software's documentation, comments, and any accompanying materials.
 * 2. Open Source: The software may be used in open source projects, as long as the project is also open source and uses a compatible open source license.
 * 3. No Warranty: The software is provided "as is," without warranty of any kind, express or implied. I will not be liable for any damages arising from the use of the software.
 * 4. No Liability: I will not be liable for any damages resulting from the use of the software, including but not limited to any direct, indirect, special, incidental, or consequential damages.
 *
 * By using this software, you agree to these terms and conditions. If you have any questions or concerns, please contact me.
 */

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
