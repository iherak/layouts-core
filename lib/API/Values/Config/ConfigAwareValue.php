<?php

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigAwareValue
{
    /**
     * Returns the config collection.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigCollection
     */
    public function getConfigCollection();

    /**
     * Returns all available configs.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs();

    /**
     * Returns the config with specified identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config
     */
    public function getConfig($identifier);

    /**
     * Returns if the config with specified identifier exists.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfig($identifier);
}