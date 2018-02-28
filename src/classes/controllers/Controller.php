<?php

namespace Classes\Controllers;

use Psr\Container\ContainerInterface;
use Slim\Views\PhpRenderer;

abstract class Controller
{
    /** @var \PDO */
    protected $db;
    /** @var PhpRenderer */
    protected $renderer;

    public function __construct(ContainerInterface $container)
    {
        $this->db = $container['db'];
        $this->renderer = $container['renderer'];
    }
}
