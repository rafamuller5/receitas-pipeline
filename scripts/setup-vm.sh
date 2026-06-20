#!/usr/bin/env bash
# setup-vm.sh
# Prepara a VM da Univates do ZERO para rodar todo o pipeline:
#   1. Instala Docker e Docker Compose (se não existirem)
#   2. Cria a rede Docker compartilhada entre os ambientes
#   3. Baixa, configura e instala o GitHub Actions Runner como serviço
#
# Uso:
#   chmod +x setup-vm.sh
#   ./setup-vm.sh <URL_DO_REPO> <TOKEN_DO_RUNNER>
#
# O TOKEN_DO_RUNNER é obtido em:
#   GitHub > seu repo > Settings > Actions > Runners > New self-hosted runner
# (o token expira em poucos minutos, gere um novo se este script falhar por isso)

set -euo pipefail

REPO_URL="${1:-}"
RUNNER_TOKEN="${2:-}"
RUNNER_DIR="$HOME/actions-runner"
RUNNER_VERSION="2.319.1"   # ajuste para a versão mais recente se preferir

if [[ -z "$REPO_URL" || -z "$RUNNER_TOKEN" ]]; then
  echo "Uso: ./setup-vm.sh <URL_DO_REPO_GITHUB> <TOKEN_DO_RUNNER>"
  echo "Ex.: ./setup-vm.sh https://github.com/seu-usuario/sistema-receitas ABCDEF123456"
  exit 1
fi

echo "==> [1/4] Instalando Docker (se necessário)..."
if ! command -v docker &> /dev/null; then
  curl -fsSL https://get.docker.com -o get-docker.sh
  sudo sh get-docker.sh
  sudo usermod -aG docker "$USER"
  rm get-docker.sh
else
  echo "Docker já instalado."
fi

echo "==> [2/4] Criando rede Docker compartilhada 'receitas-net'..."
docker network inspect receitas-net >/dev/null 2>&1 || docker network create receitas-net

echo "==> [3/4] Baixando e configurando o GitHub Actions Runner..."
mkdir -p "$RUNNER_DIR"
cd "$RUNNER_DIR"

if [[ ! -f "./config.sh" ]]; then
  curl -o actions-runner.tar.gz -L \
    "https://github.com/actions/runner/releases/download/v${RUNNER_VERSION}/actions-runner-linux-x64-${RUNNER_VERSION}.tar.gz"
  tar xzf ./actions-runner.tar.gz
  rm actions-runner.tar.gz
fi

./config.sh --url "$REPO_URL" --token "$RUNNER_TOKEN" --name "vm-univates" --unattended --replace

echo "==> [4/4] Instalando o runner como serviço (inicia junto com a VM)..."
sudo ./svc.sh install
sudo ./svc.sh start

echo ""
echo "✅ Setup concluído."
echo "   - Verifique em GitHub > Settings > Actions > Runners se o runner aparece 'Idle'."
echo "   - Os ambientes Homolog/Prod ainda NÃO existem; eles são criados quando o"
echo "     workflow do GitHub Actions rodar pela primeira vez (etapa H do trabalho)."
echo "   - Não esqueça de cadastrar os secrets SONAR_TOKEN no repositório do GitHub."
