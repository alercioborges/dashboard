#!/bin/bash

PHP_PATH="/c/wamp64/bin/php/php8.3.22/php.exe"

SCRIPTS=(
  "/c/wamp64/www/dashboard/app/cli/remember_me.php"
  "/c/wamp64/www/dashboard/app/cli/forgot_password.php"
)

echo "Iniciando limpeza de registros..."
echo "----------------------------------"

for SCRIPT in "${SCRIPTS[@]}"; do
  echo "➡️ Executando: $SCRIPT"
  "$PHP_PATH" "$SCRIPT"
done

echo "----------------------------------"
echo "Processo finalizado."
echo ""
read -p "Pressione ENTER para fechar..."
