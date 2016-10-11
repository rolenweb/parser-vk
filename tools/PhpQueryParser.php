<?php

namespace app\commands\tools;

use phpQuery;

/**
 * @property phpQueryObject $document
 */
class PhpQueryParser implements ParserInterface
{
	private $document;
	/**
	 * @inheritdoc
	 */
	public function in($content, $content_type)
	{
		$this->document = phpQuery::newDocument($content, $content_type);
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function find($pattern)
	{
		return $this->document->find($pattern)->elements;
	}

	/**
	 * @inheritdoc
	 */
	public function first()
	{
		return $this->document->current();
	}

	/**
	 * @inheritdoc
	 */
	public function each(\Closure $closure)
	{
		$this->document->each($closure);
	}

	public function setDocument($document)
	{
		$this->document = $document;
		return $this;
	}
}
