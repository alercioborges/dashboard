<?php

namespace App\Services;

class PaginationService
{
    public function paginate(int $page, int $limit, int $total): array
    {
        $page  = max(1, $page);
        $limit = max(1, $limit);

        return [
            'numPages' => (int) ceil($total / $limit),
            'offset'   => ($page - 1) * $limit
        ];
    }
}