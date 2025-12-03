<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DateTimeNormalizer implements NormalizerInterface
{
    public function normalize($object, ?string $format = null, array $context = []): string
    {
        return $object->format('Y-m-d H:i:s');
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof \DateTimeInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \DateTimeInterface::class => true,
        ];
    }
}