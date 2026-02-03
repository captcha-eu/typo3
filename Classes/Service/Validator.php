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

class Validator
{
	// solution parameter
	private const SOLUTION_PARAM = 'captcha_at_solution';

	protected ClientInterface $client;
	protected Configuration $configuration;
	protected LoggerInterface $logger;
	protected RequestFactory $requestFactory;

	public function __construct(
		ClientInterface $client,
		LoggerInterface $logger,
		Configuration $configuration,
		RequestFactory $requestFactory
	) {
		$this->client = $client;
		$this->configuration = $configuration;
		$this->logger = $logger;
		$this->requestFactory = $requestFactory;
	}

	public function checkSolution($solution, $key, $endpoint) {
		
		try {
			$payload = [
				'headers' => [
					'Content-Type' => 'application/json',
					'Rest-Key' => $key
				],
				'body' => $solution
			];
		
			$response = $this->requestFactory->request(
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
