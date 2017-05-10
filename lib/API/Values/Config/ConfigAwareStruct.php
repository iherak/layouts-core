<?php

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigAwareStruct
{
    /**
     * Sets the config struct to this struct.
     *
     * @param $identifier
     * @param \Netgen\BlockManager\API\Values\Config\ConfigStruct $configStruct
     */
    public function setConfigStruct($identifier, ConfigStruct $configStruct);

    /**
     * Returns if the struct has a config struct with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigStruct($identifier);

    /**
     * Gets the config struct with provided identifier.
     *
     * @param $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If config struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct
     */
    public function getConfigStruct($identifier);

    /**
     * Returns all config structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct[]
     */
    public function getConfigStructs();
}