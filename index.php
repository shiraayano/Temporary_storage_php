<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'function.php';

// 获取用户id（文件夹名），默认值为adouzi
$userId = isset($_GET['id']) ? $_GET['id'] : 'shiraayano';

// 上传文件处理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadResult = uploadFile($_FILES['file'], $userId);
}

// 删除文件处理
if (isset($_POST['delete'])) {
    $deleteResult = deleteFile($_POST['file'], $userId);
}

$files = listFiles($userId);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-title" content="文件暂存">
    <meta itemprop="name" content="文件暂存">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="author" content="文件暂存">
    <meta name="keywords" content="白綾乃,shiraayano,ShiRaAYaNo,阿豆子,ADouZi,adouzi,文件暂存">
    <meta name="description" content="白綾乃的主页">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="https://thirdqq.qlogo.cn/g?b=qq&nk=1355967533&s=640">
    <title>文件暂存网站</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0 auto;
            max-width: 600px;
            padding: 20px;
        }
        .upload-form {
            margin-bottom: 20px;
        }
        .file-list {
            margin-top: 20px;
        }
        .file-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<h1>文件暂存网站</h1>

<div class="upload-form">
    <h2>创建用户文件夹并上传文件</h2>
    <form action="index.php" method="GET">
        <label for="userId">请输入用户ID：</label>
        <input type="text" name="id" id="userId" placeholder="adouzi" value="<?php echo htmlspecialchars($userId); ?>" required>
        <button type="submit">创建/切换用户</button>
    </form>

    <form action="index.php?id=<?php echo htmlspecialchars($userId); ?>" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">上传</button>
    </form>
    <?php if (isset($uploadResult)): ?>
        <p><?php echo $uploadResult; ?></p>
    <?php endif; ?>
</div>

<div class="file-list">
    <h2>用户 <?php echo htmlspecialchars($userId); ?> 的已上传文件</h2>
    <?php if (count($files) > 0): ?>
        <ul>
            <?php foreach ($files as $file): ?>
                <li class="file-item">
                    <a href="function.php?id=<?php echo htmlspecialchars($userId); ?>&file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
                    <form action="index.php?id=<?php echo htmlspecialchars($userId); ?>" method="POST" style="display:inline;">
                        <input type="hidden" name="file" value="<?php echo htmlspecialchars($file); ?>">
                        <button type="submit" name="delete">删除</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>暂时没有文件。</p>
    <?php endif; ?>
    <?php if (isset($deleteResult)): ?>
        <p><?php echo $deleteResult; ?></p>
    <?php endif; ?>
</div>
    <p>
    用户须知：<br>
    本站提供文件暂存服务，可以用来临时储存小文件。<br>
    本站默认为公开，如果需要私用，用上面的注册或者切换用户就行，用户名就相当于你的私有key<br>
    最后，本站会不定期清除文件，虽然一般不会管。别把重要东西往上传，上面的删除是真的删除，我没写类似回收站的机制，望周知。<br>
    最后的最后，别闲的没事搞渗透。
    </p>
    <p>Copyright © shiraayano All Rights Reserved</p>
</body>
</html>
