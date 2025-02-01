<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Deployer;

task('db:dump', function () {
    checkRemoteDir('{{remote_store}}');
    run("mysqldump -u'{{remote_db_user}}' -p'{{remote_db_pass}}' '{{remote_db_name}}' | gzip -9 > '{{remote_store}}/brotherhood.sql.gz'");
})->desc('원격 데이터베이스를 덤프합니다.');

task('db:download', function () {
    checkRemoteFile('{{remote_store}}/brotherhood.sql.gz');
    checkLocalDir('{{local_store}}');
    downloadFromHost(
        src: "{{remote_store}}/brotherhood.sql.gz",
        dst: "{{local_store}}/brotherhood.sql.gz",
    );
})->desc('덩프돤 .sql.gz 파일을 로컬로 다운로드 받습니다.');
