<?php
class FileSystem {
    private $root;
    private $virtualFS;
    private $fsFile;
    
    public function __construct() {
        $this->root = SYSTEM_ROOT . '/virtual_fs';
        $this->fsFile = SYSTEM_ROOT . '/data/filesystem.json';
        $this->loadFS();
    }
    
    public function init() {
        if (!file_exists($this->root)) {
            mkdir($this->root, 0777, true);
        }
        
        if (!file_exists($this->fsFile)) {
            $this->virtualFS = [
                'C:' => [
                    'type' => 'dir',
                    'content' => [
                        'Windows' => [
                            'type' => 'dir',
                            'content' => []
                        ],
                        'Program Files' => [
                            'type' => 'dir',
                            'content' => []
                        ],
                        'Users' => [
                            'type' => 'dir',
                            'content' => [
                                'Public' => [
                                    'type' => 'dir',
                                    'content' => []
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $this->saveFS();
        }
    }
    
    private function loadFS() {
        if (file_exists($this->fsFile)) {
            $this->virtualFS = json_decode(file_get_contents($this->fsFile), true);
        } else {
            $this->virtualFS = [];
        }
    }
    
    private function saveFS() {
        file_put_contents($this->fsFile, json_encode($this->virtualFS, JSON_PRETTY_PRINT));
    }
    
    public function createFile($path, $content = '') {
        $parts = explode('/', trim($path, '/'));
        $current = &$this->virtualFS;
        
        for ($i = 0; $i < count($parts) - 1; $i++) {
            if (!isset($current[$parts[$i]])) {
                return false;
            }
            $current = &$current[$parts[$i]]['content'];
        }
        
        $filename = end($parts);
        if (isset($current[$filename])) {
            return false;
        }
        
        $current[$filename] = [
            'type' => 'file',
            'content' => $content,
            'created' => time(),
            'modified' => time()
        ];
        
        $this->saveFS();
        return true;
    }
    
    public function readFile($path) {
        $parts = explode('/', trim($path, '/'));
        $current = $this->virtualFS;
        
        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                return false;
            }
            $current = $current[$part]['content'];
        }
        
        return $current;
    }
    
    public function writeFile($path, $content) {
        $parts = explode('/', trim($path, '/'));
        $current = &$this->virtualFS;
        
        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                return false;
            }
            $current = &$current[$part]['content'];
        }
        
        $current = $content;
        $this->saveFS();
        return true;
    }
    
    public function createDirectory($path) {
        $parts = explode('/', trim($path, '/'));
        $current = &$this->virtualFS;
        
        for ($i = 0; $i < count($parts) - 1; $i++) {
            if (!isset($current[$parts[$i]])) {
                return false;
            }
            $current = &$current[$parts[$i]]['content'];
        }
        
        $dirname = end($parts);
        if (isset($current[$dirname])) {
            return false;
        }
        
        $current[$dirname] = [
            'type' => 'dir',
            'content' => [],
            'created' => time(),
            'modified' => time()
        ];
        
        $this->saveFS();
        return true;
    }
    
    public function listDirectory($path) {
        $parts = explode('/', trim($path, '/'));
        $current = $this->virtualFS;
        
        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                return false;
            }
            $current = $current[$part]['content'];
        }
        
        return array_keys($current);
    }
    
    public function delete($path) {
        $parts = explode('/', trim($path, '/'));
        $current = &$this->virtualFS;
        
        for ($i = 0; $i < count($parts) - 1; $i++) {
            if (!isset($current[$parts[$i]])) {
                return false;
            }
            $current = &$current[$parts[$i]]['content'];
        }
        
        $name = end($parts);
        if (!isset($current[$name])) {
            return false;
        }
        
        unset($current[$name]);
        $this->saveFS();
        return true;
    }
} 