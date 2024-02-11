<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class OrderInvalidItemsException extends Exception
{
	private array $invalidItems;

	public function __construct(array $invalidItems, string $message = "", int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->invalidItems = $invalidItems;
	}

	public function render()
	{
		return response(
			[
				'exception'		=> self::class,
				'invalid_items'	=> $this->invalidItems,
				'message'		=> $this->message
			],
			Response::HTTP_UNPROCESSABLE_ENTITY
		);
	}

	/** use custom message instead of default exception */
	public function report(): bool
	{
		return true;
	}
}
