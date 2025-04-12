<?php
class Kernel {
    private $fs;
    private $users;
    private $processes;
    private $log;
    private $syscalls;
    
    public function __construct() {
        $this->fs = new FileSystem();
        $this->users = new UserSystem();
        $this->processes = new ProcessManager();
        $this->log = new SystemLog();
        $this->syscalls = new SystemCalls();
    }
    
    public function boot() {
        $this->log->write("System booting...");
        
        // Initialize filesystem
        $this->fs->init();
        
        // Check for users.json
        if (!file_exists(SYSTEM_ROOT . '/data/users.json')) {
            $this->createDefaultUser();
        }
        
        // Initialize process manager
        $this->processes->init();
        
        // Register system calls
        $this->registerSystemCalls();
        
        $this->log->write("System booted successfully");
    }
    
    private function createDefaultUser() {
        $defaultUser = [
            'admin' => [
                'password' => password_hash('admin', PASSWORD_DEFAULT),
                'rights' => 'admin'
            ]
        ];
        
        if (!is_dir(SYSTEM_ROOT . '/data')) {
            mkdir(SYSTEM_ROOT . '/data', 0777, true);
        }
        
        file_put_contents(
            SYSTEM_ROOT . '/data/users.json',
            json_encode($defaultUser, JSON_PRETTY_PRINT)
        );
    }
    
    private function registerSystemCalls() {
        // Register basic system calls
        $this->syscalls->register('open', [$this->fs, 'open']);
        $this->syscalls->register('save', [$this->fs, 'save']);
        $this->syscalls->register('exec', [$this->processes, 'start']);
        $this->syscalls->register('kill', [$this->processes, 'kill']);
    }
    
    public function getFS() {
        return $this->fs;
    }
    
    public function getUsers() {
        return $this->users;
    }
    
    public function getProcesses() {
        return $this->processes;
    }
    
    public function getLog() {
        return $this->log;
    }
    
    public function getSyscalls() {
        return $this->syscalls;
    }
} 