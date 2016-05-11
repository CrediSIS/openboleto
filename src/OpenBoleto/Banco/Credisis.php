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

class Credisis extends BoletoAbstract
{
	/**
	 * Código do banco
	 * @var string
	 */
	protected $codigoBanco = '097';

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
	 * Define o código do Convênio: “1000000”
	 */
	protected $convenio;

	/**
	 * Define o Código do Cooperado no DECLACOB. Ex: “1234”
	 */
	protected $codigoCooperado;

	protected function gerarNossoNumero()
	{
		/**
		 *
		 * Nosso Número. Composição do Nosso Número: AAAAAAABBBBCCCCCC
		 * AAAAAAA = Código do Convênio: “1000000”
		 * BBBB = Código do Cooperado no DECLACOB. Ex: “1234”
		 * CCCCCC = Sequencial do Título (nunca pode repetir). Ex: “000001”
		 *
		 * Esta Sequencia seria representada por:
		 * “10000001234000001”
		 */

		$convenio        = self::zeroFill($this->getConvenio(), 7);
		$codigoCooperado = self::zeroFill($this->getCodigoCooperado(), 4);
		$sequencial      = self::zeroFill($this->getSequencial(), 6);

		return $convenio . $codigoCooperado . $sequencial;

	}


	public function getCampoLivre()
	{
		/**
		 * MONTAGEM DO CAMPO LIVRE
		 * .............................................................
		 * N.       POSICOES    PICTURE  USAGE   CONTEUDO
		 * .............................................................
		 * 01       020 a 025   9/006/   Display  Fixo: “000000” (Zeros)
		 * 02       026 a 042   9/017/   Display  NossoNúmero, sem DV
		 * 03       043 a 044   9/002/   Display  Fixo: “18”
		 */

		//Fixo: “000000” (Zeros)
		$campoLivre = '000000';

		//NossoNumero
		$codigoCooperado = self::zeroFill($this->getCodigoCooperado(), 4);
		$convenio        = self::zeroFill($this->getConvenio(), 7);
		$sequencial      = self::zeroFill($this->getSequencial(), 6);

		$campoLivre .= $convenio . $codigoCooperado . $sequencial;

		//Fixo: "18"
		$campoLivre .= '18';

		return $campoLivre;
	}

	public function setConvenio($convenio)
	{
		$this->convenio = $convenio;
	}


	public function getConvenio()
	{
		return $this->convenio;
	}

	public function setCodigoCooperado($codigoCooperado)
	{
		$this->codigoCooperado = $codigoCooperado;

		return $this;
	}

	public function getCodigoCooperado()
	{
		return $this->codigoCooperado;
	}

	public function getLogoBanco()
	{
		$filename = "credisis/{$this->getAgencia()}.png";
		$filepath = $this->getResourcePath() . '/images/';

		return file_exists($filepath . $filename) ? $filename : $this->logoBanco;

	}


}