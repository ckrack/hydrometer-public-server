#!/bin/bash
source ./.env

echo "connecting to tcp-server on 127.0.0.1 $TCP_API_PORT"

command='{
"name":"eSpindel","ID":"1068313","angle":71.11,"temperature":18.25,"battery":5.5,"gravity":22.22, "token": "d2386084dc3c35e3fb70"}

'
# push command to tcp server with netcat
echo $command | nc 127.0.0.1 $TCP_API_PORT
