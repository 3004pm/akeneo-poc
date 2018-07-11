<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Helper\LocaleHelper;
use App\CacheBundle\Controller\AbstractController;
use App\CustomEntityBundle\Entity\DependentAttribute;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ReferenceDataController.
 *
 * @package App\ApiBundle\Controller\Gesco
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DependentAttributeController extends AbstractController
{
    /** @var CustomEntityRepository */
    protected $dependentAttributeRepository;

    /**
     * getAllAction
     *
     * @param Request $request The request variable.
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAllAction(Request $request): JsonResponse
    {
        $cacheItem = $this->getCachedItem($request);

        if (false === $cacheItem->isHit()) {
            $dependentAttributes = $this->getDependentAttributeRepository()->findAll();

            $normalizedData = [];

            /** @var DependentAttribute $dependentAttribute */
            foreach ($dependentAttributes as $dependentAttribute) {
                $normalizedData[] = $this->getNormalizer()->normalize(
                    $dependentAttribute,
                    'gesco',
                    ['locale' => LocaleHelper::DEFAULT_LOCALE]
                );
            }

            $this->getRedisManager()->setCacheData($cacheItem, $normalizedData);
        }

        return new JsonResponse($this->getRedisManager()->getCacheData($cacheItem));
    }

    /**
     * Get get product with family and attributes data.
     *
     * @param Request $request The request variable.
     * @param string  $attributeCode
     * @param string  $optionCode
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getByAttributeAndOptionAction(
        Request $request,
        string $attributeCode,
        string $optionCode
    ): JsonResponse {
        $cacheItem = $this->getCachedItem($request);

        if (false === $cacheItem->isHit()) {
            $dependentAttributes = $this->getDependentAttributeRepository()->findBy(
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
                $normalizedData[] = $this->getNormalizer()->normalize(
                    $dependentAttribute,
                    'gesco',
                    ['locale' => LocaleHelper::DEFAULT_LOCALE]
                );
            }

            $this->getRedisManager()->setCacheData($cacheItem, $normalizedData);
        }

        return new JsonResponse($this->getRedisManager()->getCacheData($cacheItem));
    }

    /**
     * Retrieve dependent attribute repository from container.
     *
     * @return CustomEntityRepository
     */
    protected function getDependentAttributeRepository(): CustomEntityRepository
    {
        if (null === $this->dependentAttributeRepository) {
            $this->dependentAttributeRepository = $this->getService('app_custom_entity.repository.dependentattribute');
        }

        return $this->dependentAttributeRepository;
    }
}
