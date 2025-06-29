# localstack

### 1. 必要なら環境変数をセット

```
export AWS_ACCESS_KEY_ID=test
export AWS_SECRET_ACCESS_KEY=test
```

### 2. ファイル一覧を確認

```
aws --endpoint-url=http://localhost:4566 s3 ls s3://sample-bucket/
```
