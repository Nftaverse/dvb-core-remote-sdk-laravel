<?php

namespace DVB\Core\SDK\DTOs;

interface PaginatedResponseInterface
{
    public function getCursor(): ?string;
    public function hasMore(): bool;
    public function getItems(): ?array;
}