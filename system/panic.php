<?php
function panic($message) {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Windows 95 - Fatal Error</title>
        <style>
            body {
                background-color: #000080;
                color: white;
                font-family: 'MS Sans Serif', Arial, sans-serif;
                margin: 0;
                padding: 20px;
            }
            .panic-screen {
                max-width: 600px;
                margin: 50px auto;
                background-color: #000080;
                border: 2px solid #c0c0c0;
                padding: 20px;
            }
            .panic-header {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .panic-message {
                font-size: 16px;
                line-height: 1.5;
                margin-bottom: 20px;
            }
            .panic-info {
                font-size: 14px;
                color: #c0c0c0;
            }
            .panic-button {
                background-color: #c0c0c0;
                border: 2px solid #ffffff;
                border-right-color: #808080;
                border-bottom-color: #808080;
                padding: 5px 15px;
                cursor: pointer;
                font-family: 'MS Sans Serif', Arial, sans-serif;
            }
            .panic-button:active {
                border: 2px solid #808080;
                border-right-color: #ffffff;
                border-bottom-color: #ffffff;
            }
        </style>
    </head>
    <body>
        <div class="panic-screen">
            <div class="panic-header">A fatal error has occurred</div>
            <div class="panic-message"><?php echo htmlspecialchars($message); ?></div>
            <div class="panic-info">
                <p>Technical information:</p>
                <p>*** STOP: 0x0000007B</p>
                <p>Time: <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>
            <button class="panic-button" onclick="window.location.reload()">Restart</button>
        </div>
    </body>
    </html>
    <?php
    exit;
} 