<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

trait HydratorTrait
{
    /**
     * @var \Netgen\BlockManager\Utils\Hydrator
     */
    private static $__hydrator;

    /**
     * Creates a new instance of a class on which the method is called
     * and return the object hydrated with provided data.
     *
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data)
    {
        self::initHydrator();

        return self::$__hydrator->hydrate($data, new static());
    }

    /**
     * Hydrates the object instance with provided data.
     *
     * @param array $data
     *
     * @return static
     */
    public function hydrate(array $data)
    {
        self::initHydrator();

        return self::$__hydrator->hydrate($data, $this);
    }

    /**
     * Initializes the hydrator in case it was not initialized yet.
     */
    private static function initHydrator(): void
    {
        self::$__hydrator = self::$__hydrator ?? new Hydrator();
    }
}
