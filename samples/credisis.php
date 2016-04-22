<?php

require '../autoloader.php';

use OpenBoleto\Agente;
use OpenBoleto\Banco\Credisis;

$sacado  = new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
$cedente = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$boleto = new Credisis([
	// Parâmetros obrigatórios
	'dataVencimento'         => new DateTime(),
	'valor'                  => 23.00,
	'sequencial'             => 100001,
	'sacado'                 => $sacado,
	'cedente'                => $cedente,
	'agencia'                => '0001', // Até 4 dígitos
	'carteira'               => 18,
	'conta'                  => '0000002', // Até 8 dígitos
	'contaDv'                => 7,
	'convenio'               => '1000000', // 4, 6 ou 7 dígitos
	'codigoCooperado'        => '0027',
	'descricaoDemonstrativo' => [
		'Compra de materiais cosméticos',
		'Compra de alicate',
	],
	'instrucoes'             => [
		'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
		'Não receber após o vencimento.',
	],
]);

echo $boleto->getOutput();
