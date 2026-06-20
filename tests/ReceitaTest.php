<?php
declare(strict_types=1);
namespace Tests;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

class ReceitaTest extends TestCase
{
    private function validarReceita(array $dados): array
    {
        $erros = [];
        if (empty(trim($dados['nome'] ?? ''))) $erros[] = 'Nome é obrigatório';
        if (strlen($dados['nome'] ?? '') > 150) $erros[] = 'Nome deve ter no máximo 150 caracteres';
        if (!in_array($dados['tipo_receita'] ?? '', ['doce', 'salgada'])) $erros[] = 'Tipo de receita inválido';
        if (!isset($dados['custo']) || !is_numeric($dados['custo']) || (float)$dados['custo'] < 0) $erros[] = 'Custo deve ser um valor positivo';
        if (empty($dados['data_registro'] ?? '')) $erros[] = 'Data de registro é obrigatória';
        elseif (!\DateTime::createFromFormat('Y-m-d', $dados['data_registro'])) $erros[] = 'Data de registro inválida';
        return $erros;
    }

    private function validarUsuario(array $dados): array
    {
        $erros = [];
        if (empty(trim($dados['nome'] ?? ''))) $erros[] = 'Nome é obrigatório';
        if (empty(trim($dados['login'] ?? ''))) $erros[] = 'Login é obrigatório';
        if (strlen($dados['login'] ?? '') > 50) $erros[] = 'Login deve ter no máximo 50 caracteres';
        if (strlen($dados['senha'] ?? '') < 6) $erros[] = 'Senha deve ter no mínimo 6 caracteres';
        if (!in_array($dados['situacao'] ?? '', ['ativo', 'inativo'])) $erros[] = 'Situação inválida';
        return $erros;
    }

    private function formatarCusto(float $valor): string { return 'R$ ' . number_format($valor, 2, ',', '.'); }
    private function sanitizarBusca(string $input): string { return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8'); }
    private function calcularEstatisticas(array $receitas): array
    {
        $total = count($receitas);
        $doces = count(array_filter($receitas, fn($r) => $r['tipo_receita'] === 'doce'));
        $salgadas = count(array_filter($receitas, fn($r) => $r['tipo_receita'] === 'salgada'));
        $custos = array_column($receitas, 'custo');
        $media = $total > 0 ? array_sum($custos) / $total : 0.0;
        return compact('total', 'doces', 'salgadas', 'media');
    }
    private function gerarHashSenha(string $senha): string { return md5($senha); }
    private function verificarSenha(string $senha, string $hash): bool { return md5($senha) === $hash; }

    #[Test] #[Group('validacao')]
    public function receitaValidaNaoGeraErros(): void
    {
        $this->assertEmpty($this->validarReceita(['nome'=>'Bolo','tipo_receita'=>'doce','custo'=>35.50,'data_registro'=>'2024-01-10']));
    }
    #[Test] #[Group('validacao')]
    public function nomeVazioGeraErro(): void
    {
        $this->assertContains('Nome é obrigatório', $this->validarReceita(['nome'=>'','tipo_receita'=>'doce','custo'=>10.0,'data_registro'=>'2024-01-01']));
    }
    #[Test] #[Group('validacao')]
    public function tipoInvalidoGeraErro(): void
    {
        $this->assertContains('Tipo de receita inválido', $this->validarReceita(['nome'=>'Teste','tipo_receita'=>'bebida','custo'=>10.0,'data_registro'=>'2024-01-01']));
    }
    #[Test] #[Group('validacao')]
    public function custoNegativoGeraErro(): void
    {
        $this->assertContains('Custo deve ser um valor positivo', $this->validarReceita(['nome'=>'Teste','tipo_receita'=>'salgada','custo'=>-5.0,'data_registro'=>'2024-01-01']));
    }
    #[Test] #[Group('validacao')]
    public function dataInvalidaGeraErro(): void
    {
        $this->assertContains('Data de registro inválida', $this->validarReceita(['nome'=>'Teste','tipo_receita'=>'doce','custo'=>10.0,'data_registro'=>'32/13/2024']));
    }
    #[Test] #[Group('validacao')]
    public function nomeExcessivamenteGrandeGeraErro(): void
    {
        $this->assertContains('Nome deve ter no máximo 150 caracteres', $this->validarReceita(['nome'=>str_repeat('A',151),'tipo_receita'=>'doce','custo'=>10.0,'data_registro'=>'2024-01-01']));
    }
    #[Test] #[Group('validacao')]
    public function tipoSalgadaEhAceito(): void
    {
        $this->assertEmpty($this->validarReceita(['nome'=>'Coxinha','tipo_receita'=>'salgada','custo'=>48.0,'data_registro'=>'2024-01-15']));
    }
    #[Test] #[Group('validacao')]
    public function custoZeroEhValido(): void
    {
        $this->assertEmpty($this->validarReceita(['nome'=>'Gratis','tipo_receita'=>'doce','custo'=>0.0,'data_registro'=>'2024-01-01']));
    }
    #[Test] #[Group('formatacao')]
    public function formatacaoCustoCorreto(): void
    {
        $this->assertSame('R$ 35,50', $this->formatarCusto(35.50));
        $this->assertSame('R$ 1.200,00', $this->formatarCusto(1200.00));
        $this->assertSame('R$ 0,99', $this->formatarCusto(0.99));
    }
    #[Test] #[Group('seguranca')]
    public function sanitizacaoRemoveTagsHtml(): void
    {
        $input = '<script>alert("xss")</script>Bolo';
        $saida = $this->sanitizarBusca($input);
        $this->assertStringNotContainsString('<script>', $saida);
        $this->assertStringContainsString('Bolo', $saida);
    }
    #[Test] #[Group('seguranca')]
    public function sanitizacaoEscapaAspas(): void
    {
        $saida = $this->sanitizarBusca("O'Brien");
        $this->assertStringNotContainsString('<', $saida);
    }
    #[Test] #[Group('estatisticas')]
    public function calculoEstatisticasCorreto(): void
    {
        $receitas = [['tipo_receita'=>'doce','custo'=>20.0],['tipo_receita'=>'salgada','custo'=>40.0],['tipo_receita'=>'doce','custo'=>30.0]];
        $stats = $this->calcularEstatisticas($receitas);
        $this->assertSame(3, $stats['total']);
        $this->assertSame(2, $stats['doces']);
        $this->assertSame(1, $stats['salgadas']);
        $this->assertEqualsWithDelta(30.0, $stats['media'], 0.01);
    }
    #[Test] #[Group('estatisticas')]
    public function estatisticasListaVaziaRetornaZeros(): void
    {
        $stats = $this->calcularEstatisticas([]);
        $this->assertSame(0, $stats['total']);
        $this->assertSame(0.0, $stats['media']);
    }
    #[Test] #[Group('usuario')]
    public function usuarioValidoNaoGeraErros(): void
    {
        $this->assertEmpty($this->validarUsuario(['nome'=>'Maria','login'=>'maria','senha'=>'maria123','situacao'=>'ativo']));
    }
    #[Test] #[Group('usuario')]
    public function senhaCurtaGeraErro(): void
    {
        $this->assertContains('Senha deve ter no mínimo 6 caracteres', $this->validarUsuario(['nome'=>'Teste','login'=>'teste','senha'=>'123','situacao'=>'ativo']));
    }
    #[Test] #[Group('usuario')]
    public function situacaoInvalidaGeraErro(): void
    {
        $this->assertContains('Situação inválida', $this->validarUsuario(['nome'=>'Teste','login'=>'teste','senha'=>'senha123','situacao'=>'bloqueado']));
    }
    #[Test] #[Group('seguranca')]
    public function hashSenhaGeradoCorretamente(): void
    {
        $hash = $this->gerarHashSenha('admin123');
        $this->assertSame(32, strlen($hash));
        $this->assertNotSame('admin123', $hash);
    }
    #[Test] #[Group('seguranca')]
    public function verificacaoSenhaCorreta(): void
    {
        $hash = md5('admin123');
        $this->assertTrue($this->verificarSenha('admin123', $hash));
        $this->assertFalse($this->verificarSenha('errada', $hash));
    }
    #[Test] #[Group('busca')]
    public function filtroBuscaRetornaReceitasCorretas(): void
    {
        $receitas = [['nome'=>'Bolo de Chocolate','tipo_receita'=>'doce'],['nome'=>'Coxinha','tipo_receita'=>'salgada']];
        $filtrado = array_filter($receitas, fn($r) => stripos($r['nome'], 'bolo') !== false);
        $this->assertCount(1, $filtrado);
    }
    #[Test] #[Group('busca')]
    public function filtroTipoRetornaApenasDoces(): void
    {
        $receitas = [['tipo_receita'=>'doce'],['tipo_receita'=>'salgada'],['tipo_receita'=>'doce']];
        $doces = array_filter($receitas, fn($r) => $r['tipo_receita'] === 'doce');
        $this->assertCount(2, $doces);
    }
}
