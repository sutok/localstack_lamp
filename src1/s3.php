<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// S3クライアント設定
$s3 = new S3Client([
    'version'     => 'latest',
    'region'      => getenv('DEFAULT_REGION') ?: 'ap-northeast-1',
    'endpoint'    => getenv('ENDPOINT') ?: 'http://localstack:4566',
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key'    => getenv('CREDENTIALS_KEY') ?: 'test',
        'secret' => getenv('CREDENTIALS_SECRET') ?: 'test',
    ],
]);

$bucket = 'sample-bucket';

// バケット作成（存在しない場合のみ）
try {
    $s3->createBucket(['Bucket' => $bucket]);
} catch (AwsException $e) {
    // 既に存在する場合は無視
}

// ファイルアップロード処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_file'])) {
    $file = $_FILES['upload_file'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $key = basename($file['name']);
        try {
            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $file['tmp_name'],
                'ACL'    => 'public-read',
            ]);
            $msg = 'アップロード成功: ' . htmlspecialchars($key);
        } catch (AwsException $e) {
            $msg = 'アップロード失敗: ' . $e->getMessage();
        }
    } else {
        $msg = 'ファイルアップロードエラー';
    }
}

// ファイル一覧取得
$objects = [];
try {
    $result = $s3->listObjectsV2(['Bucket' => $bucket]);
    if (!empty($result['Contents'])) {
        $objects = $result['Contents'];
    }
} catch (AwsException $e) {
    $msg = '一覧取得失敗: ' . $e->getMessage();
}

// ファイルダウンロード処理
if (isset($_GET['download'])) {
    $key = $_GET['download'];
    try {
        $result = $s3->getObject([
            'Bucket' => $bucket,
            'Key'    => $key,
        ]);
        header('Content-Type: ' . $result['ContentType']);
        header('Content-Disposition: attachment; filename="' . $key . '"');
        echo $result['Body'];
        exit;
    } catch (AwsException $e) {
        $msg = 'ダウンロード失敗: ' . $e->getMessage();
    }
}

// 削除処理
if (isset($_POST['delete_key'])) {
    try {
        $s3->deleteObject([
            'Bucket' => $bucket,
            'Key'    => $_POST['delete_key'],
        ]);
        echo "削除成功: " . htmlspecialchars($_POST['delete_key']);
        // ページをリロードして一覧を更新
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (AwsException $e) {
        echo "削除失敗: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>S3ファイルアップロード・一覧</title>
</head>
<body>
<h1>S3ファイルアップロード・一覧</h1>
<?php if (!empty($msg)) echo '<p>' . htmlspecialchars($msg) . '</p>'; ?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="upload_file" required>
    <button type="submit">アップロード</button>
</form>
<h2>アップロード済みファイル</h2>
<ul>
<?php foreach ($objects as $obj): ?>
    <li>
        <?php echo htmlspecialchars($obj['Key']); ?>
        [<a href="?download=<?php echo urlencode($obj['Key']); ?>">ダウンロード</a>]
        <form method="post" style="display:inline;">
            <input type="hidden" name="delete_key" value="<?php echo htmlspecialchars($obj['Key']); ?>">
            <button type="submit">削除</button>
        </form>
    </li>
<?php endforeach; ?>
</ul>
</body>
</html> 
