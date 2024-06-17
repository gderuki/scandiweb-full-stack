<?php


/**
 * Class ServiceLocator
 *
 * Implements a service locator pattern to manage dependencies.
 * Allows registering services by interface names and retrieving them.
 *
 * Sample usage:
 * ```
 * $locator = new ServiceLocator();
 * $locator->register(IService::class, new ServiceImplementation());
 * $service = $locator->get(IService::class);
 * ```
 */
class ServiceLocator
{
    private $services = [];

    /**
     * Registers a service implementation with a given interface.
     *
     * @param string $interface The interface name to register the service under.
     * @param mixed $implementation The service implementation, which can be an object instance or a closure that returns an instance.
     */
    public function register($interface, $implementation)
    {
        $this->services[$interface] = $implementation;
    }

    /**
     * Retrieves a service by its interface name.
     *
     * If the service is provided as a closure, the closure is invoked to create the service instance upon the first retrieval.
     * Subsequent retrievals will return the same instance.
     *
     * @param string $interface The interface name of the service to retrieve.
     * @return mixed The service implementation associated with the interface.
     * @throws InvalidArgumentException If no service is registered under the given interface name.
     */
    public function get($interface)
    {
        if (!isset($this->services[$interface])) {
            throw new InvalidArgumentException("No service registered for interface: $interface");
        }

        if ($this->services[$interface] instanceof Closure) {
            $this->services[$interface] = $this->services[$interface]();
        }

        return $this->services[$interface];
    }
}
