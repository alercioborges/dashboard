<?php

namespace App\Services;

class PaginationService
{
    public function paginate(int $page, int $limit, int $total): array
    {
        return [
            'numPages' => (int) ceil($total / $limit),
            'offset'   => ($page - 1) * $limit
        ];
    }
}
