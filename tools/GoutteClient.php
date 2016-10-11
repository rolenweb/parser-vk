<?php

namespace app\commands\tools;

use Goutte\Client;

class GoutteClient extends Client implements ClientInterface
{
	private $url;
	private $method = 'GET';

	/**
	 * @inheritdoc
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getContent()
	{
		$this->request($this->method, $this->url);
		return $this->getInternalResponse()->getContent();
	}

	public function getContentType()
	{
		return $this->getInternalResponse()->getHeader('Content-Type');
	}
}
