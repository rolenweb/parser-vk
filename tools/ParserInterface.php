<?php

namespace tools;

interface ParserInterface {
	
	/**
	 * Set content to work with
	 * @param string $content
	 * @param string $content_type
	 * @return \console\tools\ParserInterface;
	 */
	public function in($content, $content_type);

	/**
	 * Set pattern to look for
	 * @param string $pattern
	 * @return \console\tools\ParserInterface;
	 */
	public function find($pattern);

	/**
	 * Get HTML of first match
	 * @return string
	 */
	public function first();

	/**
	 * Invokes $closure for each matched element
	 * @param Closure $closure
	 */
	public function each(\Closure $closure);
}