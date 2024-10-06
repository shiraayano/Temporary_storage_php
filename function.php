<?php
define('BASE_UPLOAD_DIR', 'uploads/');

// 安全地获取文件路径，防止路径注入
function safePath($path) {
    $realBase = realpath(BASE_UPLOAD_DIR);
    $realUserPath = realpath($path);

    // 检查请求的路径是否在合法的目录中
    return $realUserPath && strpos($realUserPath, $realBase) === 0;
}

// 上传文件函数
function uploadFile($file, $userId)
{
    // 防止路径穿越，确保用户ID合法
    $userId = basename($userId); // 只允许获取最后一部分
    $userDir = BASE_UPLOAD_DIR . $userId . '/';

    // 确保上传目录存在
    if (!is_dir($userDir)) {
        if (!mkdir($userDir, 0755, true)) {
            return '用户目录创建失败。';
        }
    }

    // 限制上传文件类型，不允许上传 .php 文件
    $fileName = basename($file['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'txt', 'pdf', 'h', 'exe', 'py', 'zip', '7z', 'rar', 'epub', 'c', 'doc', 'docx']; // 限制允许的文件类型

    if (!in_array($fileExt, $allowedExtensions)) {
        return '禁止上传此类文件。';
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return '文件上传错误。';
    }

    $filePath = $userDir . $fileName;

    // 检查文件是否已存在
    if (file_exists($filePath)) {
        return '文件已存在。';
    }

    // 移动上传的文件
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return '文件上传成功。';
    } else {
        return '文件上传失败。';
    }
}

// 列出指定用户目录中的文件
function listFiles($userId)
{
    // 防止路径穿越，确保用户ID合法
    $userId = basename($userId);
    $userDir = BASE_UPLOAD_DIR . $userId . '/';

    // 检查用户目录是否存在
    if (!is_dir($userDir)) {
        return [];
    }

    // 列出目录中的文件，并过滤掉 .php 文件和文件夹
    $files = array_diff(scandir($userDir), ['.', '..']);
    $files = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) !== 'php' && !is_dir($file);
    });

    return $files;
}

// 删除文件函数
function deleteFile($fileName, $userId)
{
    // 防止路径穿越，确保用户ID合法
    $userId = basename($userId);
    $userDir = BASE_UPLOAD_DIR . $userId . '/';
    $filePath = $userDir . basename($fileName);

    // 检查文件路径是否安全
    if (!safePath($filePath)) {
        return '无法删除文件，非法路径。';
    }

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            return '文件已成功删除。';
        } else {
            return '文件删除失败。';
        }
    } else {
        return '文件不存在。';
    }
}

// 下载文件
if (isset($_GET['file']) && isset($_GET['id'])) {
    $userId = basename($_GET['id']);
    $fileName = basename($_GET['file']);
    $filePath = BASE_UPLOAD_DIR . $userId . '/' . $fileName;

    // 检查文件路径是否安全
    if (!safePath($filePath) || !file_exists($filePath)) {
        echo '文件不存在或路径非法。';
        exit;
    }

    // 向用户提供文件下载
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $fileName);
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}
