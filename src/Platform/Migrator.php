<?php

namespace Indicio\Database\Platform;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class Migrator
{
    /**
     * @var SchemaTool
     */
    protected $schema;

    /**
     * @var string[]
     */
    protected $container = [];

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->schema = new SchemaTool($em);
    }

    /**
     * @param $class
     *
     * @throws \InvalidArgumentException
     */
    public function register($class)
    {
        if(! class_exists($class)) {
            throw new \InvalidArgumentException('Could not find class: '.$class);
        }

        $this->container[] = $class;
    }

    public function createSchemaSql()
    {
        return $this->schema->getCreateSchemaSql($this->containerMetadata());
    }

    public function updateSchemaSql()
    {
        return $this->schema->getUpdateSchemaSql($this->containerMetadata(), true);
    }

    public function dropSchemaSql()
    {
        return $this->schema->getDropSchemaSQL($this->containerMetadata());
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function createSchema()
    {
        $this->schema->createSchema($this->containerMetadata());
    }
    
    public function updateSchema()
    {
        $this->schema->updateSchema($this->containerMetadata(), true);
    }

    public function dropSchema()
    {
        $this->schema->dropSchema($this->containerMetadata());
    }

    protected function containerMetadata()
    {
        return array_map(function($className) {
            return $this->em->getClassMetadata($className);
        }, $this->container);
    }
}
