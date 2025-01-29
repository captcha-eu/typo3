<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Service;

use CaptchaEU\Typo3\Configuration;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\ServerRequest;

class Validator
{
	// solution parameter
	private const SOLUTION_PARAM = 'captcha_at_solution';

	protected ClientInterface $client;
	protected Configuration $configuration;
	protected LoggerInterface $logger;

	public function __construct(ClientInterface $client, LoggerInterface $logger, Configuration $configuration) {
		$this->client = $client;
		$this->configuration = $configuration;
		$this->logger = $logger;
	}

  public function checkSolution($solution, $key, $endpoint) {
      $ch = curl_init($endpoint);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $solution);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Rest-Key: ' . $key));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($ch);
      curl_close($ch);

      $resultObject = json_decode($result);
      if ($resultObject->success) {
        return true;
      } else {
        return false;
      }
    }





	// validate given solution
	public function validate($solution = '') {
		// return if not enabled (eg. keys not set)
		if(!$this->configuration->isEnabled()) {
			return false;
		}

		// solution not set or empty
		if(empty($solution)) {
			return false;
		}

    $result = $this->checkSolution($solution, $this->configuration->getKeyREST(), $this->configuration->getEPValidate());
    return $result;
	}
}
