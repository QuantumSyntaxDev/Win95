<?php
class Explorer {
    private $currentPath = 'C:';
    
    public function __construct() {
        global $kernel;
        if (!$kernel->getUsers()->getCurrentUser()) {
            header('Location: index.php');
            exit;
        }
        
        if (isset($_GET['path'])) {
            $this->currentPath = $_GET['path'];
        }
    }
    
    public function render() {
        global $kernel;
        $files = $kernel->getSyscalls()->call('sys_list', $this->currentPath);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Explorer - <?php echo htmlspecialchars($this->currentPath); ?></title>
            <style>
                .explorer-window {
                    background-color: #c0c0c0;
                    border: 2px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    width: 800px;
                    position: absolute;
                    top: 50px;
                    left: 50px;
                }
                .explorer-title {
                    background-color: #000080;
                    color: white;
                    padding: 5px;
                    font-weight: bold;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .explorer-title button {
                    background-color: #c0c0c0;
                    border: 1px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                }
                .explorer-menu {
                    padding: 5px;
                    border-bottom: 1px solid #808080;
                }
                .explorer-menu button {
                    background-color: #c0c0c0;
                    border: 1px solid #ffffff;
                    border-right-color: #808080;
                    border-bottom-color: #808080;
                    padding: 2px 10px;
                    cursor: pointer;
                }
                .explorer-toolbar {
                    padding: 5px;
                    border-bottom: 1px solid #808080;
                    display: flex;
                    align-items: center;
                }
                .explorer-toolbar input {
                    margin-left: 10px;
                    padding: 2px;
                    width: 300px;
                }
                .explorer-content {
                    display: flex;
                    height: 500px;
                }
                .explorer-tree {
                    width: 200px;
                    border-right: 1px solid #808080;
                    padding: 10px;
                    overflow-y: auto;
                }
                .explorer-files {
                    flex-grow: 1;
                    padding: 10px;
                    overflow-y: auto;
                }
                .file-icon {
                    display: inline-block;
                    width: 100px;
                    text-align: center;
                    margin: 10px;
                    cursor: pointer;
                }
                .file-icon img {
                    width: 32px;
                    height: 32px;
                }
                .file-icon span {
                    display: block;
                    font-size: 12px;
                    margin-top: 5px;
                }
                .folder-tree {
                    list-style: none;
                    padding-left: 20px;
                }
                .folder-tree li {
                    margin: 5px 0;
                    cursor: pointer;
                }
                .folder-tree li:before {
                    content: '📁';
                    margin-right: 5px;
                }
            </style>
        </head>
        <body>
            <div class="explorer-window">
                <div class="explorer-title">
                    <span>Explorer - <?php echo htmlspecialchars($this->currentPath); ?></span>
                    <div>
                        <button onclick="minimizeExplorer()">_</button>
                        <button onclick="maximizeExplorer()">□</button>
                        <button onclick="closeExplorer()">×</button>
                    </div>
                </div>
                <div class="explorer-menu">
                    <button onclick="fileMenu()">File</button>
                    <button onclick="editMenu()">Edit</button>
                    <button onclick="viewMenu()">View</button>
                    <button onclick="helpMenu()">Help</button>
                </div>
                <div class="explorer-toolbar">
                    <button onclick="goBack()">←</button>
                    <button onclick="goForward()">→</button>
                    <button onclick="goUp()">↑</button>
                    <input type="text" id="addressBar" value="<?php echo htmlspecialchars($this->currentPath); ?>"
                           onkeypress="if(event.keyCode===13)navigateTo(this.value)">
                </div>
                <div class="explorer-content">
                    <div class="explorer-tree">
                        <ul class="folder-tree">
                            <li onclick="navigateTo('C:')">My Computer (C:)</li>
                            <?php $this->renderFolderTree(); ?>
                        </ul>
                    </div>
                    <div class="explorer-files">
                        <?php
                        if ($files !== false) {
                            foreach ($files as $file) {
                                $this->renderFileIcon($file);
                            }
                        } else {
                            echo "Unable to read directory.";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <script>
                function navigateTo(path) {
                    window.location.href = 'explorer.php?path=' + encodeURIComponent(path);
                }
                
                function goBack() {
                    window.history.back();
                }
                
                function goForward() {
                    window.history.forward();
                }
                
                function goUp() {
                    const currentPath = '<?php echo addslashes($this->currentPath); ?>';
                    const parentPath = currentPath.split('/').slice(0, -1).join('/');
                    if (parentPath) {
                        navigateTo(parentPath);
                    }
                }
                
                function minimizeExplorer() {
                    document.querySelector('.explorer-window').style.display = 'none';
                }
                
                function maximizeExplorer() {
                    const win = document.querySelector('.explorer-window');
                    if (win.style.width === '100%') {
                        win.style.width = '800px';
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
                
                function closeExplorer() {
                    window.close();
                }
                
                function fileMenu() {
                    // Add file menu functionality
                }
                
                function editMenu() {
                    // Add edit menu functionality
                }
                
                function viewMenu() {
                    // Add view menu functionality
                }
                
                function helpMenu() {
                    alert('Explorer Help\n\nThis is the file explorer.');
                }
            </script>
        </body>
        </html>
        <?php
    }
    
    private function renderFileIcon($file) {
        $isDirectory = strpos($file, '.') === false;
        $icon = $isDirectory ? '📁' : '📄';
        ?>
        <div class="file-icon" onclick="navigateTo('<?php echo addslashes($this->currentPath . '/' . $file); ?>')">
            <div><?php echo $icon; ?></div>
            <span><?php echo htmlspecialchars($file); ?></span>
        </div>
        <?php
    }
    
    private function renderFolderTree() {
        global $kernel;
        $files = $kernel->getSyscalls()->call('sys_list', $this->currentPath);
        
        if ($files !== false) {
            foreach ($files as $file) {
                if (strpos($file, '.') === false) { // Is directory
                    ?>
                    <li onclick="navigateTo('<?php echo addslashes($this->currentPath . '/' . $file); ?>')">
                        <?php echo htmlspecialchars($file); ?>
                    </li>
                    <?php
                }
            }
        }
    }
}

// Initialize and render Explorer
$explorer = new Explorer();
$explorer->render(); 