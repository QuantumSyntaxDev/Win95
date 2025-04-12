<?php
class SystemLog {
    private $logFile;
    private $maxLogSize = 5242880; // 5MB
    
    public function __construct() {
        $this->logFile = SYSTEM_ROOT . '/data/system.log';
        $this->initLogFile();
    }
    
    private function initLogFile() {
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
        
        if (!file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
        
        // Rotate log if it's too big
        if (filesize($this->logFile) > $this->maxLogSize) {
            $this->rotateLog();
        }
    }
    
    private function rotateLog() {
        $backupFile = $this->logFile . '.' . date('Y-m-d-H-i-s') . '.bak';
        rename($this->logFile, $backupFile);
        file_put_contents($this->logFile, '');
        
        // Keep only last 5 backup files
        $backups = glob($this->logFile . '.*.bak');
        if (count($backups) > 5) {
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $toDelete = array_slice($backups, 0, count($backups) - 5);
            foreach ($toDelete as $file) {
                unlink($file);
            }
        }
    }
    
    public function write($message, $level = 'info') {
        $timestamp = date('Y-m-d H:i:s');
        $pid = getmypid();
        $user = 'system';
        
        global $kernel;
        if ($kernel && $kernel->getUsers()) {
            $currentUser = $kernel->getUsers()->getCurrentUser();
            if ($currentUser) {
                $user = $currentUser['username'];
            }
        }
        
        $logEntry = sprintf(
            "[%s] [%s] [PID:%d] [%s] %s\n",
            $timestamp,
            strtoupper($level),
            $pid,
            $user,
            $message
        );
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        // Check if we need to rotate after writing
        if (filesize($this->logFile) > $this->maxLogSize) {
            $this->rotateLog();
        }
        
        return true;
    }
    
    public function read($lines = 100) {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $logs = file($this->logFile);
        $logs = array_reverse($logs);
        return array_slice($logs, 0, $lines);
    }
    
    public function clear() {
        file_put_contents($this->logFile, '');
        return true;
    }
    
    public function getLogPath() {
        return $this->logFile;
    }
} 