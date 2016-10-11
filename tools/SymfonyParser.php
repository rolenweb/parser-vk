<?php

namespace tools;

use Symfony\Component\DomCrawler\Crawler;

require 'ParserInterface.php';

class SymfonyParser extends Crawler implements ParserInterface
{
	/**
	 * @inheritdoc
	 */
	public function in($content, $content_type)
	{
		$this->addContent($content, $content_type);
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function find($pattern)
	{
		return $this->filter($pattern)->getIterator()->getArrayCopy();
	}

	/**
	 * @inheritdoc
	 */
	public function findChild($pattern)
	{
		return $this->filter($pattern)->children();
	}

	/**
	 * @inheritdoc
	 */
	public function findSrc($pattern)
	{
		return $this->filter($pattern)->extract(array('src'));
	}

	/**
	 * @inheritdoc
	 */
	public function findHref($pattern)
	{
		return $this->filter($pattern)->extract(array('href'));
	}

	/**
	 * @inheritdoc
	 */
	public function findAttr($pattern,$attr)
	{
		return $this->filter($pattern)->extract(array($attr));
	}

	/**
	 * @inheritdoc
	 */
	public function findImages($pattern)
	{
		return $this->filter($pattern)->images();
	}

	/**
	 * @inheritdoc
	 */
	public function findHtml($pattern)
	{	
		return $this->filter($pattern)->each(function ($node) {
         	return $node->html();          
        });
	}
	

}
