<?php
class ProcessManager {
    private $processes = [];
    private $nextPID = 1;
    private $processFile;
    
    public function __construct() {
        $this->processFile = SYSTEM_ROOT . '/data/processes.json';
        $this->loadProcesses();
    }
    
    public function init() {
        // Start system processes
        $this->startProcess('system', 'System');
        $this->startProcess('desktop', 'Desktop');
    }
    
    private function loadProcesses() {
        if (file_exists($this->processFile)) {
            $this->processes = json_decode(file_get_contents($this->processFile), true);
            if (!empty($this->processes)) {
                $this->nextPID = max(array_column($this->processes, 'pid')) + 1;
            }
        }
    }
    
    private function saveProcesses() {
        file_put_contents($this->processFile, json_encode($this->processes, JSON_PRETTY_PRINT));
    }
    
    public function startProcess($name, $type = 'app') {
        $pid = $this->nextPID++;
        
        $process = [
            'pid' => $pid,
            'name' => $name,
            'type' => $type,
            'state' => 'running',
            'memory' => 0,
            'startTime' => time()
        ];
        
        $this->processes[$pid] = $process;
        $this->saveProcesses();
        
        return $pid;
    }
    
    public function killProcess($pid) {
        if (!isset($this->processes[$pid])) {
            return false;
        }
        
        // Don't allow killing system processes
        if ($this->processes[$pid]['type'] === 'system') {
            return false;
        }
        
        unset($this->processes[$pid]);
        $this->saveProcesses();
        return true;
    }
    
    public function listProcesses() {
        return $this->processes;
    }
    
    public function getProcess($pid) {
        return isset($this->processes[$pid]) ? $this->processes[$pid] : null;
    }
    
    public function updateProcessState($pid, $state) {
        if (!isset($this->processes[$pid])) {
            return false;
        }
        
        $this->processes[$pid]['state'] = $state;
        $this->saveProcesses();
        return true;
    }
    
    public function updateProcessMemory($pid, $memory) {
        if (!isset($this->processes[$pid])) {
            return false;
        }
        
        $this->processes[$pid]['memory'] = $memory;
        $this->saveProcesses();
        return true;
    }
} 