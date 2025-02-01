<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Deployer;

task('theme:dump', function () {
    checkRemoteDir('{{remote_store}}');
    run("cd {{remote_wp_root}}/wp-content && tar czf {{remote_store}}/themes.tar.gz themes");
})->desc('테마 파일을 전부 압축합니다.');

task('theme:download', function () {
    checkRemoteFile('{{remote_store}}/themes.tar.gz');
    checkLocalDir('{{local_store}}');
    downloadFromHost(
        src: "{{remote_store}}/themes.tar.gz",
        dst: "{{local_store}}/themes.tar.gz",
    );
})->desc('백업된 테마 파일을 다운로드합니다.');
