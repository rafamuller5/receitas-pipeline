#!/usr/bin/env bash
# setup-vm.sh
# Script único para preparar a VM da Univates DO ZERO, sem nenhuma ação manual além
# de colar o token quando solicitado. Faz tudo:
#   1. Clona o repositório do projeto
#   2. Instala Docker
#   3. Cria a rede Docker compartilhada
#   4. Baixa, configura e instala o GitHub Actions Runner como serviço
#
# Uso (numa VM limpa, sem nada instalado):
#   curl -o setup-vm.sh -L https://raw.githubusercontent.com/rafamuller5/receitas-pipeline/main/scripts/setup-vm.sh
#   chmod +x setup-vm.sh
#   ./setup-vm.sh
#
# O script vai pedir o token do runner na hora (gere em:
# GitHub > seu repo > Settings > Actions > Runners > New self-hosted runner).
# O token expira em poucos minutos — só gere quando for de fato rodar este script.

set -euo pipefail

REPO_URL="https://github.com/rafamuller5/receitas-pipeline.git"
REPO_DIR="$HOME/receitas-pipeline"
RUNNER_DIR="$HOME/actions-runner"
RUNNER_VERSION="2.335.1"

echo "==> [1/5] Clonando o repositório..."
if [[ -d "$REPO_DIR/.git" ]]; then
  echo "Repositório já existe em $REPO_DIR, atualizando..."
  git -C "$REPO_DIR" pull
else
  git clone "$REPO_URL" "$REPO_DIR"
fi
cd "$REPO_DIR"

echo "==> [2/5] Instalando Docker (se necessário)..."
if ! command -v docker &> /dev/null; then
  curl -fsSL https://get.docker.com -o get-docker.sh
  sudo sh get-docker.sh
  rm get-docker.sh
else
  echo "Docker já instalado."
fi

# Garante que o usuário está no grupo docker, independente de o Docker já existir ou não.
# Necessário porque o runner do GitHub Actions vai rodar comandos "docker" sem sudo
# mais adiante (no pipeline.yml). A mudança de grupo só vale para processos novos
# (por isso usamos "sudo docker" no restante deste script).
if ! id -nG "$USER" | grep -qw docker; then
  echo "Adicionando $USER ao grupo docker..."
  sudo usermod -aG docker "$USER"
  echo "⚠️  Grupo docker adicionado. Pode ser necessário fazer logout/login para o"
  echo "    seu próprio terminal usar 'docker' sem sudo - mas o runner do GitHub"
  echo "    Actions (instalado como serviço no passo 5) já vai funcionar corretamente."
fi

echo "==> [3/5] Criando rede Docker compartilhada 'receitas-net'..."
sudo docker network inspect receitas-net >/dev/null 2>&1 || sudo docker network create receitas-net

echo "==> [4/5] Configurando o GitHub Actions Runner..."
echo ""
echo "Gere o token agora em: $REPO_URL (sem .git) > Settings > Actions > Runners > New self-hosted runner"
read -rp "Cole o token do runner aqui: " RUNNER_TOKEN

if [[ -z "$RUNNER_TOKEN" ]]; then
  echo "Nenhum token informado. Abortando."
  exit 1
fi

mkdir -p "$RUNNER_DIR"
cd "$RUNNER_DIR"

if [[ ! -f "./config.sh" ]]; then
  curl -o actions-runner.tar.gz -L \
    "https://github.com/actions/runner/releases/download/v${RUNNER_VERSION}/actions-runner-linux-x64-${RUNNER_VERSION}.tar.gz"
  tar xzf ./actions-runner.tar.gz
  rm actions-runner.tar.gz
fi

./config.sh --url "$REPO_URL" --token "$RUNNER_TOKEN" --name "vm-univates" --unattended --replace

echo "==> [5/5] Instalando o runner como serviço (inicia junto com a VM)..."
sudo ./svc.sh install
sudo ./svc.sh start

echo ""
echo "✅ Setup concluído."
echo "   - Repositório clonado em: $REPO_DIR"
echo "   - Verifique em GitHub > Settings > Actions > Runners se o runner aparece 'Idle'."
echo "   - Os ambientes Homolog/Prod ainda NÃO existem; eles são criados quando o"
echo "     workflow do GitHub Actions rodar pela primeira vez (etapa H do trabalho)."
echo "   - Não esqueça de cadastrar o secret SONAR_TOKEN no repositório do GitHub."
