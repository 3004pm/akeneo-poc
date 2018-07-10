<?php

namespace App\ApiBundle\Controller\Gesco;

use Akeneo\Component\StorageUtils\Detacher\BulkObjectDetacherInterface;
use App\ApiBundle\Helper\LocaleHelper;
use App\CustomEntityBundle\Entity\DependentAttribute;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Class ReferenceDataController.
 *
 * @package App\ApiBundle\Controller\Gesco
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DependentAttributeController
{
    use NormalizerAwareTrait;

    /** @var CustomEntityRepository */
    protected $dependentAttributeRepository;
    /** @var BulkObjectDetacherInterface */
    protected $objectDetacher;

    /**
     * ProductModelController constructor.
     *
     * @param CustomEntityRepository      $dependentAttributeRepository
     * @param BulkObjectDetacherInterface $objectDetacher
     */
    public function __construct(
        CustomEntityRepository $dependentAttributeRepository,
        BulkObjectDetacherInterface $objectDetacher
    ) {
        $this->dependentAttributeRepository = $dependentAttributeRepository;
        $this->objectDetacher               = $objectDetacher;
    }

    /**
     * Get get product with family and attributes data.
     *
     * @param string $attributeCode
     * @param string $optionCode
     *
     * @return JsonResponse
     */
    public function getByAttributeAndOptionAction(
        string $attributeCode,
        string $optionCode
    ): JsonResponse {
        $dependentAttributes = $this->dependentAttributeRepository->findBy(
            [
                'attributeCode' => $attributeCode,
                'optionCode'    => $optionCode,
            ]
        );

        if (empty($dependentAttributes)) {
            throw new NotFoundHttpException(
                sprintf(
                    'No dependent attribute found for attribute code "%s" and option code "%s".',
                    $attributeCode,
                    $optionCode
                )
            );
        }

        $normalizedData = [];

        /** @var DependentAttribute $dependentAttribute */
        foreach ($dependentAttributes as $dependentAttribute) {
            $normalizedData[] = $this->normalizer->normalize(
                $dependentAttribute,
                'gesco',
                ['locale' => LocaleHelper::DEFAULT_LOCALE]
            );
        }

        $this->objectDetacher->detachAll($dependentAttributes);

        return new JsonResponse($normalizedData);
    }
}
