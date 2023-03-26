<?php
/**
 * This file is respectively apart of the TopPHP project.
 *
 * Copyright (c) 2021-present James Walston and Federico Cosma
 * Some rights are reserved.
 *
 * This copyright is subject to the MIT license which
 * fully entails the details in the LICENSE file.
 */

namespace TopPHP\Components;
use TopPHP\Components\Guilds;
use TopPHP\TopPHP;

class Guild {
    public Guilds $guilds;
    protected TopPHP $parent;
    protected $fillable = [
        'id',
        'type',
        'name',
        'icon',
        'votes',
        'nsfwLevel',
        'description',
        'tags',
        'socialCount',
        'isLocked',
        'lockAuthor',
        'lockReason',
        'createdAt',
        'reviewStats',
        'iconUrl',
        'reviewScore'
    ];
    protected $needed = [
        'id',
        'type',
        'name',
        'icon',
        'votes',
        'nsfwLevel',
        'description',
        'createdAt',
        'iconUrl'
    ];

    function __construct(object $rawguild, TopPHP $parent) {
        $this->parent = $parent;
        foreach ($this->fillable as $value) {
            if (!empty($rawguild->{$value})) {
                $this->{$value} = $rawguild->{$value};
            } else {
                $this->parent->callableException('dataMissingException', [
                    "name" => "Some data are missing during the bot object creation!",
                    "description" => "Some data are missing during the bot object creation!",
                    "bot_id" => $rawguild->id,
                    "missing_data" => $value
                ], $this->parent);
                continue;
            }
        }
    }
}