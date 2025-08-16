# 会議室予約システム

このプロジェクトは Laravel 12（バックエンド）と Vue.js 3（フロントエンド）、MySQL（データベース）を使用した勤怠管理システムです。
社員の出勤・退勤・休憩管理や、勤怠データの集計を行うことができます。
管理者は勤怠状況の確認や修正、CSV 出力などが可能です。

# 機能

- 社員の出勤・退勤・休憩打刻
- 管理者用ダッシュボード
- 勤怠修正申請と承認機能
- CSV / Excel 出力機能
- ロール管理（一般社員・管理者）

# 開発環境のセットアップ

## 1. リポジトリをクローン
```bash
git clone https://github.com/mogashima/worktime.git
cd attendance-system
```

## 2. パッケージインストール
```bash
composer install
npm install
```

## 3. 環境ファイルの設定
```bash
cp .env.example .env
```

## 4. アプリケーションキー生成
```bash
php artisan key:generate
```

## 5. マイグレーション & シーディング
```bash
php artisan migrate --seed
```

## 6. フロントエンドのセットアップ
```bash
npm run build
```

## 7. 開発サーバー起動
```bash
php artisan serve
```

## 8. テストの実施
```bash
php artisan test --filter=TimeCalculatorServiceTest
```