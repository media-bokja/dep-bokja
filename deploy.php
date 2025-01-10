<?php

namespace Deployer;

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

if ('cli' !== php_sapi_name()) {
    die('This is a CLI application.');
}

// Load .env file.
$de = Dotenv::createUnsafeMutable(__DIR__);
$de->load();
$de->required([
    'LOCAL_STORE',
    'REMOTE_DB_NAME',
    'REMOTE_DB_PASS',
    'REMOTE_DB_USER',
    'REMOTE_HOST',
    'REMOTE_STORE',
    'REMOTE_USER',
    'REMOTE_WP_ROOT',
]);

set('remote_host', $_ENV['REMOTE_HOST']);

// Hosts
host($_ENV['REMOTE_HOST'])
    ->set('deploy_path', '~/dep-bokja')
    ->set('local_store', $_ENV['LOCAL_STORE'])

    // Remote
    // database
    ->set('remote_db_name', $_ENV['REMOTE_DB_NAME'])
    ->set('remote_db_pass', $_ENV['REMOTE_DB_PASS'])
    ->set('remote_db_user', $_ENV['REMOTE_DB_USER'])

    // store path
    ->set('remote_store', $_ENV['REMOTE_STORE'])

    // PHP
    ->set('remote_php', $_ENV['REMOTE_PHP'] ?? 'php')

    // User
    ->set('remote_user', $_ENV['REMOTE_USER'])
    ->set('remote_wp_cli', $_ENV['REMOTE_WP_CLI'] ?? '~/bin/wp')
    ->set('remote_wp_root', $_ENV['REMOTE_WP_ROOT'])
;

/**
 * 카페24는 rsync 지원하지 않아서 파일을 적절히 scp 처리해야 함.
 *
 * @param string $src 호스트에 있는 파일 경로
 * @param string $dst 로컬에 저장할 경로
 *
 * @return void
 * @throws Exception\RunException
 */
function downloadFromHost(string $src, string $dst): void
{
    runLocally('scp {{remote_user}}@{{remote_host}}:' . $src . ' ' . $dst);
}

// Hooks
task('db:dump', function () {
    $root = get('remote_store');
    if (!test("[ -d $root ]")) {
        run("mkdir -p $root");
    }
    run("mysqldump -u'{{remote_db_user}}' -p'{{remote_db_pass}}' '{{remote_db_name}}' | gzip -9 > '{{remote_store}}/brotherhood.sql.gz'");
})->desc('원격 데이터베이스를 덤프합니다.');

task('db:download', function () {
    if (!test("[ -f {{remote_store}}/brotherhood.sql.gz ]")) {
        error('Remote .sq.gz file not found.');
    }
    if (!testLocally("[ -d {{local_store}} ]")) {
        runLocally("mkdir -p {{local_store}}");
    }
    downloadFromHost(
        src: "{{remote_store}}/brotherhood.sql.gz",
        dst: "{{local_store}}/brotherhood.sql.gz",
    );
})->desc('덩프돤 .sql.gz 파일을 로컬로 다운로드 받습니다.');
