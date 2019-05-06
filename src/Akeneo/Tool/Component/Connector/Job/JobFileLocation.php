<?php

namespace Akeneo\Tool\Component\Connector\Job;

/**
 * Represents file location logic used for import or export job.
 *
 * @author    Benoit Jacquemont <benoit@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
final class JobFileLocation
{
    private const REMOTE_SCHEMA='PIM_REMOTE://';

    /** @var string */
    private $path;

    /** @var bool */
    private $remote;

    public function __construct(string $path, bool $remote)
    {
        $this->path = $path;
        $this->remote = $remote;
    }

    /**
     * Generate a JobFileLocation object from an encoding string
     */
    public static function buildFromEncodedLocation(string $encodedLocation): JobFileLocation
    {
        if (0 === strpos($encodedLocation, self::REMOTE_SCHEMA)) {
            $remote = true;
            $path = substr($encodedLocation, strlen(self::REMOTE_SCHEMA));
        } else {
            $remote = false;
            $path = $encodedLocation;
        }

        return new self($path, $remote);
    }

    public function isRemote(): bool
    {
        return $this->remote;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Return an encoded location. If the location is remote,
     * the path is prefixed with REMOTE_SCHEMA.
     * Otherwise, the encoded location is the path
     */
    public function encodeLocation(): string
    {
        if (true === $this->remote) {
            return self::REMOTE_SCHEMA.$this->path;
        } else {
            return $this->path;
        }
    }
}
