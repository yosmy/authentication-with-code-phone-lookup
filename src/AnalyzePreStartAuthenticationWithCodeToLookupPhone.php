<?php

namespace Yosmy;

use Yosmy\Phone\ResolveClassification;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.pre_start_authentication_with_code',
 *     ]
 * })
 */
class AnalyzePreStartAuthenticationWithCodeToLookupPhone implements AnalyzePreStartAuthenticationWithCode
{
    /**
     * @var ResolveClassification
     */
    private $resolveClassification;

    /**
     * @param ResolveClassification $resolveClassification
     */
    public function __construct(ResolveClassification $resolveClassification)
    {
        $this->resolveClassification = $resolveClassification;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        string $device,
        string $country,
        string $prefix,
        string $number
    ) {
        try {
            $classification = $this->resolveClassification->resolve(
                $country,
                $prefix,
                $number
            );
        } catch (Phone\UnresolvableClassificationException $e) {
            return;
        }

        if ($classification->isVoip()) {
            throw new DeniedAuthenticationException('El número pertenece a una operadora no permitida');
        }
    }
}
