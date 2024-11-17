<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信域名检测</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .button-group {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .button-group button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            margin: 0 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button-group button:hover {
            background-color: #45a049;
        }
        .domain-input,
        .domain-list {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }
        textarea {
            width: 50%;
            height: 150px;
            margin-top: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }
        .domain-list ul {
            width: 100%;
            list-style-type: none;
            padding: 0;
        }
        .domain-list li {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        .valid {
            background-color: #e0f7e0;
            color: #4caf50;
        }
        .invalid {
            background-color: #f8d7da;
            color: #d9534f;
        }
        .current {
            font-weight: bold;
            color: #1e7e34;
            background-color: #c8e6c9; /* Light green */
        }
        .not-using {
            background-color: #d1d1d1; /* Light gray */
            color: #333;
        }
        .log {
            margin-top: 30px;
            max-height: 200px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .log p {
            margin: 0;
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>微信域名检测系统</h1>
        <div class="button-group">
            <button onclick="checkDomainPool()">检测域名池</button>
            <button onclick="viewDomainPool()">查看域名池</button>
        </div>

        <div class="domain-input">
            <label for="domainTextArea">批量添加域名（每行一个）：</label>
            <textarea id="domainTextArea" placeholder="请输入域名，每行一个"></textarea>
            <button onclick="addDomains()">添加域名</button>
        </div>

        <div class="domain-list">
            <h3>域名池</h3>
            <ul id="domainList"></ul>
        </div>

        <div class="log">
            <h3>检测记录</h3>
            <div id="logContent"></div>
        </div>
    </div>

    <script>
        // 页面加载时自动加载域名池和检测记录
        document.addEventListener("DOMContentLoaded", function() {
            viewDomainPool();
            fetchLogs();
        });

        // 批量添加域名
        function addDomains() {
            const password = prompt("请输入管理员密码以添加域名:");
            if (password !== "adminpassword") {
                alert("密码错误，无法添加域名！");
                return;
            }

            const domainsText = document.getElementById("domainTextArea").value.trim();
            const domains = domainsText.split('\n').map(domain => domain.trim()).filter(domain => domain);

            if (domains.length === 0) {
                alert("请输入有效的域名！");
                return;
            }

            fetch('add_domain.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ domains: domains })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                viewDomainPool();  // 刷新域名池
            })
            .catch(error => {
                alert("添加域名时发生错误");
            });
        }

        // 查看当前域名池
        function viewDomainPool() {
            fetch('view_domain.php')
                .then(response => response.json())
                .then(data => {
                    const domainListElement = document.getElementById('domainList');
                    domainListElement.innerHTML = '';  // 清空现有列表

                    data.domains.forEach(domain => {
                        const li = document.createElement('li');
                        if (domain.is_current === 'current') {
                            li.classList.add('current');
                            li.textContent = domain.domain + ' (正在使用中)';
                        } else {
                            li.classList.add('not-using');
                            li.textContent = domain.domain + ' (未使用)';
                        }

                        domainListElement.appendChild(li);
                    });
                });
        }

        // 检测域名池
        function checkDomainPool() {
            fetch('check_domain.php')
                .then(response => response.json())
                .then(data => {
                    fetchLogs();  // 刷新检测记录
                    viewDomainPool();  // 刷新域名池
                })
                .catch(error => {
                    console.error("检测域名池时发生错误", error);
                });
        }

        // 获取检测记录
        function fetchLogs() {
            fetch('get_logs.php')
                .then(response => response.json())
                .then(data => {
                    const logContentElement = document.getElementById('logContent');
                    logContentElement.innerHTML = '';  // 清空现有记录

                    data.logs.forEach(log => {
                        const p = document.createElement('p');
                        p.textContent = log;
                        logContentElement.appendChild(p);
                    });
                });
        }
    </script>
</body>
</html>
