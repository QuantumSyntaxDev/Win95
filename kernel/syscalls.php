<?php
class SystemCalls {
    private $calls = [];
    
    public function register($name, $callback) {
        $this->calls[$name] = $callback;
    }
    
    public function call($name, ...$args) {
        if (!isset($this->calls[$name])) {
            throw new Exception("System call '$name' not found");
        }
        
        return call_user_func_array($this->calls[$name], $args);
    }
    
    // File system calls
    public function sys_open($path, $mode = 'r') {
        global $kernel;
        $fs = $kernel->getFS();
        
        if ($mode === 'r') {
            return $fs->readFile($path);
        } else if ($mode === 'w') {
            return true; // Just check if we can write, actual writing is done by sys_save
        }
        
        return false;
    }
    
    public function sys_save($path, $content) {
        global $kernel;
        return $kernel->getFS()->writeFile($path, $content);
    }
    
    public function sys_delete($path) {
        global $kernel;
        return $kernel->getFS()->delete($path);
    }
    
    public function sys_list($path) {
        global $kernel;
        return $kernel->getFS()->listDirectory($path);
    }
    
    // Process management calls
    public function sys_exec($app) {
        global $kernel;
        return $kernel->getProcesses()->startProcess($app);
    }
    
    public function sys_kill($pid) {
        global $kernel;
        return $kernel->getProcesses()->killProcess($pid);
    }
    
    public function sys_ps() {
        global $kernel;
        return $kernel->getProcesses()->listProcesses();
    }
    
    // User management calls
    public function sys_whoami() {
        global $kernel;
        $user = $kernel->getUsers()->getCurrentUser();
        return $user ? $user['username'] : null;
    }
    
    public function sys_login($username, $password) {
        global $kernel;
        return $kernel->getUsers()->login($username, $password);
    }
    
    public function sys_logout() {
        global $kernel;
        $kernel->getUsers()->logout();
        return true;
    }
    
    // System information calls
    public function sys_info() {
        return [
            'os' => 'Windows 95 PHP Edition',
            'version' => '1.0.0',
            'uptime' => time() - $_SERVER['REQUEST_TIME'],
            'memory' => [
                'total' => memory_get_peak_usage(true),
                'used' => memory_get_usage(true)
            ]
        ];
    }
    
    // Logging calls
    public function sys_log($message, $level = 'info') {
        global $kernel;
        return $kernel->getLog()->write($message, $level);
    }
} 