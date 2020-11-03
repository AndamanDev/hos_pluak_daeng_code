#!/bin/bash
docker exec -i lemp_mariadb mysql -uroot -proot_db --database=que-pmh < ../backup/que-pmh.sql