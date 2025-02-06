<?php

namespace App\Domain\Common\DTO;

/**
 * @property-read string $name
 * @property-read string $email
 * @property-read string $password
 *
 * @method bool issetIds()
 * @method bool issetNames()
 */
readonly class RegisterDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}
