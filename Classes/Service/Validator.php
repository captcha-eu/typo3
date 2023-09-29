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

	public function __construct(ClientInterface $client, LoggerInterface $logger) {
		$this->client = $client;
		$this->configuration = new Configuration();
		$this->logger = $logger;
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

		// get validation endpoint
		$endpointURL = $this->configuration->getEPValidate();

		try {
			// send request with solution to service host
			$response = $this->client->request('POST', $endpointURL, [
				'headers' => [
					'Rest-Key' => $this->configuration->getKeyREST(),
					'Content-Type' => 'application/json'
				],
				'body' => $solution
			]);

			// if request failed
			if(!$response) {
				// log errors
				$this->logger->error('captcha.eu validation request failed');
				// allow
				return true;
			}

			switch($response->getStatusCode()) {
				case 200: // OK
					// check solution
					$resBody = trim($response->getBody()->getContents());

					// convert to json
					try {
						// decode json & throw exception if parsing failed
						$resJSON = json_decode($resBody, false, 5, JSON_THROW_ON_ERROR);

						// verify success
						if($resJSON->success) {
							// valid solution -> allow
							return true;
						} else {
							// invalid solution -> deny
							return false;
						}
					} catch (\JsonException $e) {
						// log errors
						$this->logger->error('JsonException: ' . $e->getMessage());
						// deny
						return false;
					}
					break;
				case 403: // FORBIDDEN (rest-key invalid)
					// log
					$this->logger->error('captcha.eu - 403 - invalid rest key');
					return false;
					break;
				default:
					// log errors
					$this->logger->error('captcha.eu validation request response code: ' . $response->getStatusCode());
					// allow
					return true;
					break;
			}

		} catch (ClientException | GuzzleException $e) {
			// log errors
			$this->logger->error($e->getMessage());
			// allow
			return true;
		}
	}
}
