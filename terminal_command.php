<?php
require_once 'bootloader.php';

class TerminalCommand {
    private $kernel;
    private $currentPath;
    
    public function __construct() {
        global $kernel;
        $this->kernel = $kernel;
        $this->currentPath = $_POST['path'] ?? 'C:';
    }
    
    public function execute($command) {
        $parts = explode(' ', trim($command));
        $cmd = strtolower($parts[0]);
        $args = array_slice($parts, 1);
        
        switch ($cmd) {
            case 'dir':
            case 'ls':
                return $this->listDirectory();
            case 'cd':
                return $this->changeDirectory($args[0] ?? '');
            case 'type':
            case 'cat':
                return $this->showFile($args[0] ?? '');
            case 'echo':
                return implode(' ', $args);
            case 'cls':
            case 'clear':
                return "\x1B[2J\x1B[H";
            case 'help':
                return $this->showHelp();
            case 'whoami':
                return $this->kernel->getSyscalls()->call('sys_whoami');
            case 'ps':
                return $this->listProcesses();
            case 'kill':
                return $this->killProcess($args[0] ?? '');
            case 'mkdir':
                return $this->makeDirectory($args[0] ?? '');
            case 'del':
            case 'rm':
                return $this->deleteFile($args[0] ?? '');
            default:
                return "'{$cmd}' is not recognized as an internal or external command.";
        }
    }
    
    private function listDirectory() {
        $files = $this->kernel->getSyscalls()->call('sys_list', $this->currentPath);
        if ($files === false) {
            return "Unable to list directory.";
        }
        
        $output = "Directory of {$this->currentPath}\n\n";
        foreach ($files as $file) {
            $output .= $file . "\n";
        }
        return $output;
    }
    
    private function changeDirectory($path) {
        if (empty($path)) {
            return $this->currentPath;
        }
        
        $newPath = $this->resolvePath($path);
        if ($this->kernel->getSyscalls()->call('sys_list', $newPath) !== false) {
            $this->currentPath = $newPath;
            return '';
        }
        
        return "The system cannot find the path specified.";
    }
    
    private function showFile($filename) {
        if (empty($filename)) {
            return "File name must be specified.";
        }
        
        $path = $this->resolvePath($filename);
        $content = $this->kernel->getSyscalls()->call('sys_open', $path);
        
        if ($content === false) {
            return "The system cannot find the file specified.";
        }
        
        return $content;
    }
    
    private function listProcesses() {
        $processes = $this->kernel->getSyscalls()->call('sys_ps');
        
        $output = "PID\tName\t\tStatus\n";
        foreach ($processes as $pid => $process) {
            $output .= "{$pid}\t{$process['name']}\t\t{$process['state']}\n";
        }
        
        return $output;
    }
    
    private function killProcess($pid) {
        if (empty($pid)) {
            return "PID must be specified.";
        }
        
        if ($this->kernel->getSyscalls()->call('sys_kill', $pid)) {
            return "Process terminated successfully.";
        }
        
        return "Unable to terminate process.";
    }
    
    private function makeDirectory($dirname) {
        if (empty($dirname)) {
            return "Directory name must be specified.";
        }
        
        $path = $this->resolvePath($dirname);
        if ($this->kernel->getFS()->createDirectory($path)) {
            return "Directory created successfully.";
        }
        
        return "Unable to create directory.";
    }
    
    private function deleteFile($filename) {
        if (empty($filename)) {
            return "File name must be specified.";
        }
        
        $path = $this->resolvePath($filename);
        if ($this->kernel->getSyscalls()->call('sys_delete', $path)) {
            return "File deleted successfully.";
        }
        
        return "Unable to delete file.";
    }
    
    private function resolvePath($path) {
        if (strpos($path, ':') !== false) {
            return $path;
        }
        
        if ($path[0] === '/') {
            return substr($this->currentPath, 0, strpos($this->currentPath, ':') + 1) . $path;
        }
        
        return $this->currentPath . '/' . $path;
    }
    
    private function showHelp() {
        return "Available commands:
  DIR, LS        Lists files and directories
  CD             Changes the current directory
  TYPE, CAT      Displays the contents of a file
  ECHO           Displays a message
  CLS, CLEAR     Clears the screen
  WHOAMI         Displays current user
  PS             Lists running processes
  KILL           Terminates a process
  MKDIR          Creates a directory
  DEL, RM        Deletes a file
  HELP           Shows this help message";
    }
}

// Process the command
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
    $terminal = new TerminalCommand();
    echo $terminal->execute($_POST['command']);
} else {
    echo "Invalid request.";
} 