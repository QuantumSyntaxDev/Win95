<?php
class Notepad {
    private $content = '';
    private $filename = 'Untitled';
    
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
            <title>Notepad - <?php echo htmlspecialchars($this->filename); ?></title>
            <style>
                .notepad-window {
                    background-color: #c0c0c0;
                    border: 2px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    width: 600px;
                    position: absolute;
                    top: 50px;
                    left: 50px;
                }
                .notepad-title {
                    background-color: #000080;
                    color: white;
                    padding: 5px;
                    font-weight: bold;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .notepad-title button {
                    background-color: #c0c0c0;
                    border: 1px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                }
                .notepad-menu {
                    padding: 5px;
                    border-bottom: 1px solid #808080;
                }
                .notepad-menu button {
                    background-color: #c0c0c0;
                    border: 1px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    padding: 2px 10px;
                    cursor: pointer;
                }
                .notepad-content {
                    padding: 10px;
                }
                .notepad-textarea {
                    width: 100%;
                    height: 400px;
                    border: 1px solid #808080;
                    resize: none;
                    font-family: 'Courier New', monospace;
                    padding: 5px;
                }
            </style>
        </head>
        <body>
            <div class="notepad-window">
                <div class="notepad-title">
                    <span>Notepad - <?php echo htmlspecialchars($this->filename); ?></span>
                    <div>
                        <button onclick="minimizeNotepad()">_</button>
                        <button onclick="maximizeNotepad()">□</button>
                        <button onclick="closeNotepad()">×</button>
                    </div>
                </div>
                <div class="notepad-menu">
                    <button onclick="newFile()">File</button>
                    <button onclick="editMenu()">Edit</button>
                    <button onclick="helpMenu()">Help</button>
                </div>
                <div class="notepad-content">
                    <textarea class="notepad-textarea" id="notepadContent"><?php echo htmlspecialchars($this->content); ?></textarea>
                </div>
            </div>
            
            <script>
                function newFile() {
                    if (confirm('Do you want to save changes?')) {
                        saveFile();
                    }
                    document.getElementById('notepadContent').value = '';
                }
                
                function saveFile() {
                    const content = document.getElementById('notepadContent').value;
                    // Add AJAX call to save file
                }
                
                function minimizeNotepad() {
                    document.querySelector('.notepad-window').style.display = 'none';
                }
                
                function maximizeNotepad() {
                    const win = document.querySelector('.notepad-window');
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
                
                function closeNotepad() {
                    if (document.getElementById('notepadContent').value.trim() !== '') {
                        if (confirm('Do you want to save changes before closing?')) {
                            saveFile();
                        }
                    }
                    window.close();
                }
                
                function editMenu() {
                    // Add edit menu functionality
                }
                
                function helpMenu() {
                    alert('Notepad Help\n\nThis is a simple text editor.');
                }
            </script>
        </body>
        </html>
        <?php
    }
    
    public function loadFile($filename) {
        global $kernel;
        $content = $kernel->getSyscalls()->call('sys_open', $filename);
        if ($content !== false) {
            $this->content = $content;
            $this->filename = basename($filename);
            return true;
        }
        return false;
    }
    
    public function saveFile($filename, $content) {
        global $kernel;
        $result = $kernel->getSyscalls()->call('sys_save', $filename, $content);
        if ($result) {
            $this->filename = basename($filename);
            $this->content = $content;
            return true;
        }
        return false;
    }
}

// Initialize and render Notepad
$notepad = new Notepad();
$notepad->render(); 