<?php
class Desktop {
    private $user;
    private $processes;
    
    public function __construct() {
        global $kernel;
        $this->user = $kernel->getUsers()->getCurrentUser();
        $this->processes = $kernel->getProcesses();
    }
    
    public function render() {
        if (!$this->user) {
            $this->renderLogin();
            return;
        }
        
        $this->renderDesktop();
    }
    
    private function renderLogin() {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Windows 95 - Login</title>
            <style>
                body {
                    background-color: #008080;
                    font-family: 'MS Sans Serif', Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .login-window {
                    background-color: #c0c0c0;
                    border: 2px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    padding: 20px;
                    width: 300px;
                }
                .login-title {
                    background-color: #000080;
                    color: white;
                    padding: 5px;
                    margin: -20px -20px 20px -20px;
                    font-weight: bold;
                }
                .login-form input {
                    width: 100%;
                    padding: 5px;
                    margin-bottom: 10px;
                    border: 2px solid #808080;
                    border-right-color: #ffffff;
                    border-bottom-color: #ffffff;
                }
                .login-button {
                    background-color: #c0c0c0;
                    border: 2px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    padding: 5px 15px;
                    cursor: pointer;
                    float: right;
                }
                .login-button:active {
                    border: 2px solid #808080;
                    border-right-color: #ffffff;
                    border-bottom-color: #ffffff;
                }
            </style>
        </head>
        <body>
            <div class="login-window">
                <div class="login-title">Windows 95 Login</div>
                <form class="login-form" method="post" action="login.php">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" class="login-button">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
    
    private function renderDesktop() {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Windows 95 - Desktop</title>
            <style>
                body {
                    background-color: #008080;
                    font-family: 'MS Sans Serif', Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    height: 100vh;
                    display: flex;
                    flex-direction: column;
                }
                .desktop-icons {
                    padding: 20px;
                }
                .desktop-icon {
                    width: 70px;
                    text-align: center;
                    margin-bottom: 20px;
                    cursor: pointer;
                }
                .desktop-icon img {
                    width: 32px;
                    height: 32px;
                }
                .desktop-icon span {
                    color: white;
                    font-size: 12px;
                }
                .taskbar {
                    background-color: #c0c0c0;
                    border-top: 2px solid #ffffff;
                    padding: 5px;
                    display: flex;
                    align-items: center;
                }
                .start-button {
                    background-color: #c0c0c0;
                    border: 2px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    padding: 5px 15px;
                    cursor: pointer;
                    font-weight: bold;
                }
                .start-button:active {
                    border: 2px solid #808080;
                    border-right-color: #ffffff;
                    border-bottom-color: #ffffff;
                }
                .taskbar-items {
                    flex-grow: 1;
                    margin-left: 10px;
                }
                .taskbar-item {
                    background-color: #c0c0c0;
                    border: 2px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    padding: 5px 15px;
                    margin-right: 5px;
                    cursor: pointer;
                }
                .clock {
                    margin-left: auto;
                    padding: 5px 15px;
                }
            </style>
        </head>
        <body>
            <div class="desktop-icons">
                <div class="desktop-icon" onclick="openApp('explorer')">
                    <img src="assets/icons/explorer.png" alt="Explorer">
                    <span>Explorer</span>
                </div>
                <div class="desktop-icon" onclick="openApp('notepad')">
                    <img src="assets/icons/notepad.png" alt="Notepad">
                    <span>Notepad</span>
                </div>
                <div class="desktop-icon" onclick="openApp('terminal')">
                    <img src="assets/icons/terminal.png" alt="Terminal">
                    <span>Terminal</span>
                </div>
            </div>
            
            <div class="taskbar">
                <button class="start-button">Start</button>
                <div class="taskbar-items" id="taskbarItems"></div>
                <div class="clock" id="clock"></div>
            </div>
            
            <script>
                function updateClock() {
                    const now = new Date();
                    document.getElementById('clock').textContent = 
                        now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                }
                
                setInterval(updateClock, 1000);
                updateClock();
                
                function openApp(app) {
                    // Add window management logic here
                    console.log('Opening app:', app);
                }
            </script>
        </body>
        </html>
        <?php
    }
} 