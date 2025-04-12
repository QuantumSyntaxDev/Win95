<?php
class UserSystem {
    private $users = [];
    private $currentUser = null;
    private $usersFile;
    
    public function __construct() {
        $this->usersFile = SYSTEM_ROOT . '/data/users.json';
        $this->loadUsers();
    }
    
    private function loadUsers() {
        if (file_exists($this->usersFile)) {
            $this->users = json_decode(file_get_contents($this->usersFile), true);
        }
    }
    
    private function saveUsers() {
        file_put_contents($this->usersFile, json_encode($this->users, JSON_PRETTY_PRINT));
    }
    
    public function login($username, $password) {
        if (!isset($this->users[$username])) {
            return false;
        }
        
        if (password_verify($password, $this->users[$username]['password'])) {
            $this->currentUser = [
                'username' => $username,
                'rights' => $this->users[$username]['rights']
            ];
            $_SESSION['user'] = $this->currentUser;
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        $this->currentUser = null;
        unset($_SESSION['user']);
    }
    
    public function getCurrentUser() {
        if ($this->currentUser === null && isset($_SESSION['user'])) {
            $this->currentUser = $_SESSION['user'];
        }
        return $this->currentUser;
    }
    
    public function checkAccess($path) {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        // Admin has full access
        if ($user['rights'] === 'admin') {
            return true;
        }
        
        // Add more specific access rules here
        return true;
    }
    
    public function createUser($username, $password, $rights = 'user') {
        if (isset($this->users[$username])) {
            return false;
        }
        
        $this->users[$username] = [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'rights' => $rights
        ];
        
        $this->saveUsers();
        return true;
    }
    
    public function deleteUser($username) {
        if (!isset($this->users[$username])) {
            return false;
        }
        
        unset($this->users[$username]);
        $this->saveUsers();
        return true;
    }
    
    public function changePassword($username, $newPassword) {
        if (!isset($this->users[$username])) {
            return false;
        }
        
        $this->users[$username]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->saveUsers();
        return true;
    }
} 