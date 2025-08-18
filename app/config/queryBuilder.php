<?php

use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\FetchableInterface;
use ClanCats\Hydrahon\Query\Sql\Insert;

return function (PDO $connection) {

    $queryBuilder = null;

    if ($queryBuilder == null) {

        try {
            $queryBuilder = new Builder('mysql', function ($query, $queryString, $queryParameters) use ($connection) {
                $statement = $connection->prepare($queryString);
                $statement->execute($queryParameters);

                if ($query instanceof FetchableInterface) {
                    return $statement->fetchAll(\PDO::FETCH_ASSOC);
                } elseif ($query instanceof Insert) {
                    return $connection->lastInsertId();
                } else {
                    return $statement->rowCount();
                }
            });
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
};
