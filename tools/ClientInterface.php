<?php

namespace tools;

interface ClientInterface {
	/**
	 * Set URL
	 * @param string $url
	 * @return \console\tools\ClientInterface;
	 */
	public function setUrl($url);

	/**
	 * Get HTML content of URL
	 * @return string
	 */
	public function getContent();

	/**
	 * Returns PSR-7 Response instance
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getResponse();

	/**
	 * Returns content type
	 * @return string
	 */
	public function getContentType();

}
