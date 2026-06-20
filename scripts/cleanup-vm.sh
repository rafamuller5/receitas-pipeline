#!/usr/bin/env bash
# cleanup-vm.sh
# Reverte tudo o que o setup-vm.sh + o pipeline criaram, deixando a VM "limpa".
# Útil para demonstrar ao professor que a estrutura "não existe" antes da apresentação
# (item 1 da Validação do ambiente) e depois recriar tudo do zero.
#
# Uso:
#   chmod +x cleanup-vm.sh
#   ./cleanup-vm.sh

set -euo pipefail

echo "==> Parando e removendo contêineres de Homologação e Produção..."
sudo docker compose -f docker-compose.homolog.yml down -v --remove-orphans 2>/dev/null || true
sudo docker compose -f docker-compose.prod.yml down -v --remove-orphans 2>/dev/null || true

echo "==> Removendo imagem da aplicação..."
sudo docker rmi sistema-receitas:latest 2>/dev/null || true

echo "==> Removendo rede compartilhada 'receitas-net'..."
sudo docker network rm receitas-net 2>/dev/null || true

echo "==> Parando e removendo o GitHub Actions Runner (opcional)..."
RUNNER_DIR="$HOME/actions-runner"
if [[ -d "$RUNNER_DIR" ]]; then
  cd "$RUNNER_DIR"
  sudo ./svc.sh stop 2>/dev/null || true
  sudo ./svc.sh uninstall 2>/dev/null || true
  echo "   (o diretório $RUNNER_DIR foi mantido; remova manualmente com 'rm -rf' se quiser apagar tudo)"
fi

echo ""
echo "✅ Limpeza concluída. A VM está sem os ambientes Homolog/Prod e sem o runner ativo."
