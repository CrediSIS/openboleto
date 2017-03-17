<?php
/**
 * Created by CredisisTEAM.
 * User: hiago
 * Email: hiago.souza@credisis.com.br
 * Date: 22/04/16
 * Time: 13:59
 */

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;
use OpenBoleto\InvalidCpfCnpjException;

class Credisis extends BoletoAbstract
{
	/**
	 * Código do banco
	 * @var string
	 */
	protected $codigoBanco = '097';

    /**
     * Código da agência, composto por 4 dígitos numéricos
     * @var string
     */
    protected $agencia;

	/**
	 * Localização do logotipo do banco, referente ao diretório de imagens
	 * @var string
	 */
	protected $logoBanco = 'credisis/logo.png';

	/**
	 * Linha de local de pagamento
	 * @var string
	 */
	protected $localPagamento = 'ATE VCTO PAGAR QUALQUER BANCO. APOS SOMENTE NA REDE CREDISIS';
	/**
	 * Define as carteiras disponíveis para este banco
	 * @var array
	 */
	protected $carteiras = [ '18', '19' ];

	/**
	 * Código do convênio do cooperado.
	 */
	protected $convenio;

    protected function gerarNossoNumero()
	{
		/**
		 *
		 * Nosso Número. Composição do Nosso Número: 097DAAAACCCCCCSSSSSS
         * D = Dígito verificador, Módulo 11 dos 9 primeiros dígitos do cpf ou 8 do cnpj do beneficiário
		 * AAAA = Código da Agência: Ex: "0001"
		 * CCCCCC = Código do Convenio do cooperado. Ex: “123456”
		 * SSSSSS = Sequencial do Título (nunca pode repetir). Ex: “000001”
		 *
		 * Esta Sequencia seria representada por:
		 * “09710001123456000001”
		 */

		$cpfCnpj = $this->cedente->getDocumento();

		$digitoVerificado = self::modulo11($cpfCnpj, $this->getSize($cpfCnpj))['digito'];
		$convenio        = self::zeroFill($this->getConvenio(), 6);
		$sequencial      = self::zeroFill($this->getSequencial(), 6);

		return $this->codigoBanco . $digitoVerificado . $this->agencia . $convenio . $sequencial;

	}

	public function getCampoLivre()
	{
		/**
		 * MONTAGEM DO CAMPO LIVRE
		 * .............................................................
		 * N.       POSICOES    PICTURE  USAGE   CONTEUDO
		 * .............................................................
		 * 01       020 a 024   9/005/   Display  Fixo: "00000" (Zeros)
		 * 02       024 a 044   9/020/   Display  NossoNúmero.
		 */

		$nossonumero = $this->gerarNossoNumero();
        $campoLivre = '00000' . $nossonumero;

        return self::zeroFill($campoLivre, 25);
	}

	public function setConvenio($convenio)
	{
		$this->convenio = $convenio;
	}


	public function getConvenio()
	{
		return $this->convenio;
	}

	public function getLogoBanco()
	{
		$filename = "credisis/{$this->getAgencia()}.png";
		$filepath = $this->getResourcePath() . '/images/';

		return file_exists($filepath . $filename) ? $filename : $this->logoBanco;

	}

    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    public function getAgencia()
    {
        return $this->agencia;
    }

    public function getLocalPagamento()
    {
        return $this->localPagamento;
    }

    public function getCarteiras()
    {
        return $this->carteiras;
    }

    public function setAgencia( $agencia )
    {
        $this->agencia = $agencia;
    }

    public function getCodigoBarras()
    {
        $campoLivre = $this->getCampoLivre();
        $module11 = self::modulo11($this->codigoBanco . $this->moeda . $this->getFatorVencimento() . self::zeroFill( $this->valor * 100, 10) . $campoLivre)['digito'];

        return $this->codigoBanco . $this->moeda . $module11 . $this->getFatorVencimento() . self::zeroFill( $this->valor * 100, 10) . $campoLivre;
    }

    private function isCnpf( $value )
    {
        $regex = '/\A\d{14}\z/';

        return preg_match( $regex, $value );
    }

    private function isCpf( $value )
    {
        $regex = '/\A\d{11}\z/';

        return preg_match( $regex, $value );
    }

    private function getSize( $value )
    {
        if ( $this->isCnpf( $value ) ) {
            return 8;
        }

        if ( $this->isCpf( $value ) ) {
            return 9;
        }

        throw new InvalidCpfCnpjException('Formato do CPF/CNPJ informado é inválido, ele deve ser apenas número e conter 11 dígitos para CPF e 14 para CNPJ');
    }


}