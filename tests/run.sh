#!/bin/bash
TESTS_DIR=$( cd $( dirname "$0" ); pwd )
REPO_DIR=$( dirname $TESTS_DIR )
PHPUNIT=$REPO_DIR/vendor/bin/phpunit
PHP=/usr/local/php54/bin/php # PAY ATTENTION! Your php probably resides under path that differs from mine.

if [ ! -x $PHPUNIT ]; then
    echo 'Run `composer install` to obtain phpunit executable'
    exit 0
fi

$PHP $PHPUNIT -c $TESTS_DIR/phpunit.xml $@
