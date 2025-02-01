<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Deployer;

task('plugin:dump', function () {
    checkRemoteDir('{{remote_store}}');
    run("cd {{remote_wp_root}}/wp-content && tar czf {{remote_store}}/plugins.tar.gz plugins");
})->desc('플러그인 파일을 전부 압축합니다.');

task('plugin:download', function () {
    checkRemoteFile('{{remote_store}}/plugins.tar.gz');
    checkLocalDir('{{local_store}}');
    downloadFromHost(
        src: "{{remote_store}}/plugins.tar.gz",
        dst: "{{local_store}}/plugins.tar.gz",
    );
})->desc('백업된 플러그인 파일을 다운로드합니다.');
