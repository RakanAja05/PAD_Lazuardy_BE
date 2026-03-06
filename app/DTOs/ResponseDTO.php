<?php

namespace App\DTOs;

class ResponseDTO
{
    public mixed $payload;
    public int $code;

    public function __construct(mixed $payload, int $code)
    {
        $this->payload = $payload;
        $this->code = $code;
    }
}
