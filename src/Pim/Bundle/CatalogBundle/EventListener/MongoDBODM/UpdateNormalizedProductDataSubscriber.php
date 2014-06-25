<?php

namespace Pim\Bundle\CatalogBundle\EventListener\MongoDBODM;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM\QueryGenerator\NormalizedDataQueryGeneratorInterface;

/**
 * Sets the normalized data of a Product document when related entities are modified
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UpdateNormalizedProductDataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $queryGenerator = [];

    /**
     * Scheduled queries to apply
     *
     * @var string[]
     */
    protected $scheduledQueries = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['onFlush', 'postFlush'];
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->scheduleQueriesAfterUpdate($entity, $uow->getEntityChangeSet($entity));
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->scheduleQueriesAfterDelete($entity);
        }

        foreach ($uow->getScheduledCollectionDeletions() as $entity) {
            $this->scheduleQueriesAfterDelete($entity);
        }

        foreach ($uow->getScheduledCollectionUpdates() as $entity) {
            $this->scheduleQueriesAfterUpdate($entity, $uow->getEntityChangeSet($entity));
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $this->executeQueries();
    }

    /**
     * Schedule products related to the entity for normalized data recalculation
     *
     * @param object $entity
     */
    protected function scheduleQueriesAfterUpdate($entity, $changes)
    {
        foreach ($changes as $field => $values) {
            list($oldValue, $newValue) = $values;

            $queries = $this->generateQuery($entity, $field, $oldValue, $newValue);

            if (null !== $queries) {
                $this->scheduledQueries = array_merge(
                    $this->scheduledQueries,
                    $queries
                );
            }
        }
    }

    /**
     * Schedule products related to the entity for normalized data recalculation
     *
     * @param object $entity
     */
    protected function scheduleQueriesAfterDelete($entity)
    {
        $queries = $this->generateQuery($entity);

        if (null !== $queries) {
            $this->scheduledQueries = array_merge(
                $this->scheduledQueries,
                $queries
            );
        }
    }

    /**
     * Get queries for the given entity and updated field
     *
     * @return array|null
     */
    protected function generateQuery($entity, $field = '', $oldValue = '', $newValue = '')
    {
        foreach ($this->queryGenerators as $queryGenerator) {
            if ($queryGenerator->supports($entity, $field)) {
                return $queryGenerator->generateQuery($entity, $field, $oldValue, $newValue);
            }
        }

        return null;
    }

    public function addQueryGenerator(NormalizedDataQueryGeneratorInterface $queryGenerator)
    {
        $this->queryGenerators[] = $queryGenerator;
    }

    /**
     *
     */
    protected function executeQueries()
    {
    }
}
