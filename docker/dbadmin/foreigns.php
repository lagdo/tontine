<?php

// The SQL SELECT clauses to get labels for foreign key columns.
return [
    'tontine' => [
        'tontine' => [
            '*' => [
                'members' => [
                    'id' => [
                        'select' => fn(int $textLength) => "SUBSTR(defs.name, 1, $textLength)",
                        'search' => fn(string $search) => "defs.name ILIKE $search",
                        'joins' => ["INNER JOIN member_defs defs ON defs.id=members.def_id"],
                    ],
                ],
                'charges' => [
                    'id' => [
                        'select' => fn(int $textLength) => "SUBSTR(defs.name, 1, $textLength)",
                        'search' => fn(string $search) => "defs.name ILIKE $search",
                        'joins' => ["INNER JOIN charge_defs defs ON defs.id=charges.def_id"],
                    ],
                ],
                'pools' => [
                    'id' => [
                        'select' => fn(int $textLength) => "SUBSTR(defs.title, 1, $textLength)",
                        'search' => fn(string $search) => "defs.title ILIKE $search",
                        'joins' => ["INNER JOIN pool_defs defs ON defs.id=pools.def_id"],
                    ],
                ],
            ],
        ],
    ],
];
