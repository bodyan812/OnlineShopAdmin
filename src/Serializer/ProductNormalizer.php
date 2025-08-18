<?php

namespace App\Serializer;

use App\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class ProductNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(
        private StorageInterface $storage
    ) {}

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var Product $object */
        $mainImage = null;
        $firstMedia = $object->getMedia()->first();
        if ($firstMedia) {
            $mainImage = $this->storage->resolveUri($firstMedia, 'file');
        }

        $data = $this->normalizer->normalize($object, $format, $context);
        $data['main_image'] = $mainImage; // Добавляем только главное изображение

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Product;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Product::class => true];
    }
}
