<?php

declare(strict_types=1);

namespace CaptchaEU\Typo3\Service;

use CaptchaEU\Typo3\Configuration;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Validator
{
	// solution parameter
	private const SOLUTION_PARAM = 'captcha_at_solution';

	protected ClientInterface $client;
	protected Configuration $configuration;
	protected LoggerInterface $logger;

	public function __construct(ClientInterface $client, LoggerInterface $logger) {
		$this->client = $client;
		$this->configuration = new Configuration();
		$this->logger = $logger;
	}

	public function checkSolution($solution, $key, $endpoint) {
		
		$requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
		
		try {
			$payload = [
				'headers' => [
					'Content-Type' => 'application/json',
					'Rest-Key' => $key
				],
				'body' => $solution
			];
		
			$response = $requestFactory->request(
				$endpoint,
				'POST',
				$payload,
			);
	
			if ($response->getStatusCode() === 200) {
				$result = json_decode($response->getBody()->getContents());
				return $result->success ?? false;
			}
			
			return false;
		} catch (\Exception $e) {
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
