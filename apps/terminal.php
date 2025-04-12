<?php
class Terminal {
    private $currentPath = 'C:';
    private $history = [];
    
    public function __construct() {
        global $kernel;
        if (!$kernel->getUsers()->getCurrentUser()) {
            header('Location: index.php');
            exit;
        }
    }
    
    public function render() {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Terminal</title>
            <style>
                .terminal-window {
                    background-color: #c0c0c0;
                    border: 2px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    width: 600px;
                    position: absolute;
                    top: 50px;
                    left: 50px;
                }
                .terminal-title {
                    background-color: #000080;
                    color: white;
                    padding: 5px;
                    font-weight: bold;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .terminal-title button {
                    background-color: #c0c0c0;
                    border: 1px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                }
                .terminal-content {
                    background-color: #000000;
                    color: #ffffff;
                    font-family: 'Courier New', monospace;
                    padding: 10px;
                    height: 400px;
                    overflow-y: auto;
                }
                .terminal-input {
                    display: flex;
                    align-items: center;
                    margin-top: 5px;
                }
                .terminal-prompt {
                    color: #ffffff;
                    margin-right: 5px;
                }
                .terminal-command {
                    background-color: transparent;
                    border: none;
                    color: #ffffff;
                    font-family: 'Courier New', monospace;
                    flex-grow: 1;
                    outline: none;
                }
                .terminal-output {
                    white-space: pre-wrap;
                    margin-bottom: 5px;
                }
            </style>
        </head>
        <body>
            <div class="terminal-window">
                <div class="terminal-title">
                    <span>Terminal</span>
                    <div>
                        <button onclick="minimizeTerminal()">_</button>
                        <button onclick="maximizeTerminal()">□</button>
                        <button onclick="closeTerminal()">×</button>
                    </div>
                </div>
                <div class="terminal-content" id="terminalContent">
                    <div class="terminal-output">Windows 95 PHP Edition [Version 1.0]
Copyright (c) 2024 Your Name. All rights reserved.</div>
                    <div class="terminal-input">
                        <span class="terminal-prompt"><?php echo htmlspecialchars($this->currentPath); ?>></span>
                        <input type="text" class="terminal-command" id="commandInput" 
                               onkeydown="if(event.keyCode===13)executeCommand(this.value)">
                    </div>
                </div>
            </div>
            
            <script>
                const terminalContent = document.getElementById('terminalContent');
                const commandInput = document.getElementById('commandInput');
                let commandHistory = [];
                let historyIndex = -1;
                
                function executeCommand(command) {
                    if (!command.trim()) return;
                    
                    // Add command to history
                    commandHistory.push(command);
                    historyIndex = commandHistory.length;
                    
                    // Show command in output
                    const output = document.createElement('div');
                    output.className = 'terminal-output';
                    output.textContent = '<?php echo htmlspecialchars($this->currentPath); ?>>' + command;
                    terminalContent.insertBefore(output, document.querySelector('.terminal-input'));
                    
                    // Process command
                    fetch('terminal_command.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'command=' + encodeURIComponent(command) + '&path=' + 
                              encodeURIComponent('<?php echo htmlspecialchars($this->currentPath); ?>')
                    })
                    .then(response => response.text())
                    .then(result => {
                        const resultOutput = document.createElement('div');
                        resultOutput.className = 'terminal-output';
                        resultOutput.textContent = result;
                        terminalContent.insertBefore(resultOutput, document.querySelector('.terminal-input'));
                        terminalContent.scrollTop = terminalContent.scrollHeight;
                    });
                    
                    // Clear input
                    commandInput.value = '';
                }
                
                commandInput.addEventListener('keydown', function(e) {
                    if (e.keyCode === 38) { // Up arrow
                        if (historyIndex > 0) {
                            historyIndex--;
                            this.value = commandHistory[historyIndex];
                        }
                        e.preventDefault();
                    } else if (e.keyCode === 40) { // Down arrow
                        if (historyIndex < commandHistory.length - 1) {
                            historyIndex++;
                            this.value = commandHistory[historyIndex];
                        } else {
                            historyIndex = commandHistory.length;
                            this.value = '';
                        }
                        e.preventDefault();
                    }
                });
                
                function minimizeTerminal() {
                    document.querySelector('.terminal-window').style.display = 'none';
                }
                
                function maximizeTerminal() {
                    const win = document.querySelector('.terminal-window');
                    if (win.style.width === '100%') {
                        win.style.width = '600px';
                        win.style.height = 'auto';
                        win.style.top = '50px';
                        win.style.left = '50px';
                    } else {
                        win.style.width = '100%';
                        win.style.height = '100vh';
                        win.style.top = '0';
                        win.style.left = '0';
                    }
                }
                
                function closeTerminal() {
                    window.close();
                }
                
                // Focus input on load
                commandInput.focus();
            </script>
        </body>
        </html>
        <?php
    }
}

// Initialize and render Terminal
$terminal = new Terminal();
$terminal->render();
 