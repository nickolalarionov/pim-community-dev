<?php

namespace Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM\QueryGenerator;

/**
* Option value updated query generator
*/
class OptionValueUpdatedQueryGenerator extends AbstractQueryGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generateQuery($entity, $field, $oldValue, $newValue)
    {
        $attributeNormFields = $this->attributeNamingUtility->getPossibleAttributeCodes(
            $entity->getOption()->getAttribute(),
            ProductQueryUtility::NORMALIZED_FIELD . '.'
        );

        $queries = [];

        foreach ($attributeNormFields as $attributeNormField) {
            $queries[] = [
                [$attributeNormField => $entity->getOption()->getCode()],
                [
                    '$set' => [
                        sprintf(
                            '%s.code.optionValues.%s.value',
                            $attributeNormField,
                            $entity->getLocale()
                        ) => $newValue
                    ]
                ],
                ['multi' => true]
            ];
        }

        return $queries;
    }
}