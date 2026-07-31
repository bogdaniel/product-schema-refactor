<?php

declare(strict_types=1);

namespace ProductSchema\DTO;

final readonly class QuestionAnswer
{
    public function __construct(
        public string $question,
        public string $answer,
        public ?string $answeredBy = null,
    ) {
    }
}
