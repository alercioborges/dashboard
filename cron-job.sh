#!/bin/bash

# Caminho do PHP do Wamp
PHP_PATH="/c/wamp64/bin/php/php8.3.22/php.exe"

# Caminho do script PHP
SCRIPT_PATH="/c/wamp64/www/dashboard/app/config/cli/remember_me.php"

echo "Iniciando limpeza de registros..."
echo "----------------------------------"

"$PHP_PATH" "$SCRIPT_PATH"

echo "----------------------------------"
echo "Processo finalizado."
echo ""
read -p "Pressione ENTER para fechar..."