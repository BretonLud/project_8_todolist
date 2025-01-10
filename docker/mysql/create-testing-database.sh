#!/usr/bin/env bash

mariadb --user=root --password="$MARIADB_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS todolist_test;
    GRANT ALL PRIVILEGES ON todolist_test.* TO '$MARIADB_USER'@'%';
EOSQL