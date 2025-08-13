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
        $images = [];
        foreach ($object->getMedia() as $media) {
            $images[] = $this->storage->resolveUri($media, 'file');
        }

        $data = $this->normalizer->normalize($object, $format, $context);
        $data['images'] = $images;

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
