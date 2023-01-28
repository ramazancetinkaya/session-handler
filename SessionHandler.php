<?php
/**
 * SessionHandler
 *
 * @author [ramazancetinkaya] (https://github.com/ramazancetinkaya)
 * @date [28.01.2023]
 *
 * Copyright (c) [2023] [Ramazan Çetinkaya]
 * 
 * Software License Agreement
 * 
 *  1. Ownership and Intellectual Property Rights
 * 
 *      The Author retains all rights, title, and interest in and to the Software, including all intellectual property rights. The Software is protected by copyright laws and international copyright treaties, as well as other intellectual property laws and treaties. The Author grants you a non-transferable, non-exclusive, limited license to use the Software in accordance with the terms and conditions of this Agreement.
 *
 *  2. Permitted Use
 *
 *      The Software is licensed for use in open source and private projects. 
 *
 *      - For open source projects, the Software may be used under the terms and conditions of a compatible open source license.
 *      - For private projects, the Software may be used with the prior written permission of the Author. To request permission, please contact the Author at [github.com/ramazancetinkaya].
 *
 *  3. Restrictions
 *
 *      Avoid doing these.
 *       
 *      - Modify, translate, reverse engineer, decompile, or disassemble the Software, except to the extent that such activity is expressly permitted by applicable law;
 *      - Rent, lease, lend, sell, redistribute, or sublicense the Software;
 *      - Remove any proprietary notices or labels on the Software;
 *      - Use the Software in any manner that infringes the intellectual property rights of the Author or any third party.
 *
 *  4. Disclaimer of Warranties
 *
 *      The Software is provided "AS IS," without warranty of any kind, express or implied, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose. The Author does not warrant that the Software will meet your requirements or that the operation of the Software will be uninterrupted or error-free.
 *
 *  5. Limitation of Liability
 *
 *      To the maximum extent permitted by applicable law, in no event shall the Author be liable for any direct, indirect, incidental, special, or consequential damages arising out of or in connection with the use of the Software, even if the Author has been advised of the possibility of such damages.
 *
 *
 * Software Ownership and Attribution
 *
 *  As the author of this software, I retain ownership of the code and any associated intellectual property. However, I am happy to make the software available for use in open source and private projects, under the following conditions:
 *
 *      1. Attribution: Any use of the software in a public or open source project must clearly indicate my name and my Github profile link (https://github.com/ramazancetinkaya) as the original author of the code. This includes, but is not limited to, including my name in the software's documentation, comments, and any accompanying materials.
 *      2. Open Source: The software may be used in open source projects, as long as the project is also open source and uses a compatible open source license.
 *      3. Private Use: The software may also be used in private projects, with my express permission. If you would like to use the software in a private project, please contact me to request permission.
 *      4. No Warranty: The software is provided "as is," without warranty of any kind, express or implied. I will not be liable for any damages arising from the use of the software.
 *      5. No Liability: I will not be liable for any damages resulting from the use of the software, including but not limited to any direct, indirect, special, incidental, or consequential damages.
 *
 *
 *  By installing, copying, or otherwise using the Software, you agree to be bound by the terms of this Agreement. If you do not agree to the terms of this Agreement, do not install or use the Software. 
 *  If you have any questions or concerns, please contact me.
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
