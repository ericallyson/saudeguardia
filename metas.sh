#!/bin/bash


# Mata o processo se estiver rodando
pkill -f "php artisan metas:dispatch_pendings"

# Aguarda 2 segundos pra garantir que morreu
sleep 2

# Sobe novamente em background
cd /home2/saudeguardia/app.saudeguardia.com.br/
nohup php artisan  metas:dispatch-pending >> /home2/saudeguardia/app.saudeguardia.com.br//metas.log 2>&1 &

