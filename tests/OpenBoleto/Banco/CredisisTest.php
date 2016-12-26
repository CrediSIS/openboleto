<?php
/**
 * Created by CredisisTEAM.
 * User: jfernando
 * Date: 26/12/16
 * Time: 09:45
 */

namespace Tests\OpenBoleto\Banco;


use Exception;
use OpenBoleto\Agente;
use OpenBoleto\Banco\Credisis;
use PHPUnit\Framework\TestCase;

class CredisisTest extends TestCase
{

    public function testGeracaoBoletoCredisis()
    {
        $boleto = new Credisis([
            'dataVencimento'         => new \DateTime(),
            'valor'                  => 100,
            'sequencial'             => 10,
            'sacado'                 => $this->getPagador( ),
            'cedente'                => $this->getBeneficiario(),
            'agencia'                => '0001',
            'carteira'               => '18',
            'conta'                  => '0000000', // Até 8 dígitos
            'contaDv'                => '1',
            'convenio'               => '23',
            'dataDocumento'          => new \DateTime(),
            'dataProcessamento'      => new \DateTime(),
            'instrucoesImpressao'    => false,
            'especieDoc'             => 'DMI',
            'numeroDocumento'        => '123',
            'descontosAbatimentos'   => 0,
            'instrucoes'             => 'Informamos que a 2° via do boleto pode ser tirada no site www.credisiscobranca.com.br.'
        ]);

        $fator = $this->getFatorVencimento($boleto->getDataVencimento());

        $this->assertEquals('09730001000023000010', $boleto->getNossoNumero());
        $this->assertEquals('09790' . $fator . $this->zeroFill($boleto->getValor() * 100, 10) . $boleto->getCampoLivre(), $boleto->getCodigoBarras());
        $this->assertEquals(strlen($boleto->getCodigoBarras()), 44);
        $this->assertEquals(strlen($boleto->getLinhaDigitavel()), 54);
    }

    private function getPagador()
    {
        return new Agente( 'Teste', '95727493234', 'Rua teste', '11111111', 'São Paulo', 'SP' );
    }

    private function getBeneficiario()
    {
        return $this->getPagador();
    }

    private function getFatorVencimento($vencimento){
        $inicial = new \DateTime( '1997-10-07' );
        $fatorVencimento = $inicial->diff( $vencimento )->days;

        return $fatorVencimento;
    }

    private static function zeroFill( $valor, $digitos )
    {
        // TODO: Retirar isso daqui, e criar um método para validar os dados
        if ( strlen($valor) > $digitos ) {
            throw new Exception("O valor {$valor} possui mais de {$digitos} dígitos!");
        }

        return str_pad($valor, $digitos, '0', STR_PAD_LEFT);
    }


}