<?php

namespace Yosmy\Test;

use Yosmy;
use PHPUnit\Framework\TestCase;
use LogicException;

class AnalyzePreStartAuthenticationWithCodeToLookupPhoneTest extends TestCase
{
    public function testStart()
    {
        $device = 'device';
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';

        $classification = $this->createMock(Yosmy\Phone\Classification::class);

        $classification->expects($this->once())
            ->method('isVoip')
            ->with()
            ->willReturn(false);

        $resolveClassification = $this->createMock(Yosmy\Phone\ResolveClassification::class);

        $resolveClassification->expects($this->once())
            ->method('resolve')
            ->with(
                $country,
                $prefix,
                $number
            )
            ->willReturn($classification);

        $analyzePreStartAuthenticationWithCodeToLookupPhone = new Yosmy\AnalyzePreStartAuthenticationWithCodeToLookupPhone(
            $resolveClassification
        );

        try {
            $analyzePreStartAuthenticationWithCodeToLookupPhone->analyze(
                $device,
                $country,
                $prefix,
                $number
            );
        } catch (Yosmy\DeniedAuthenticationException $e) {
            throw new LogicException();
        }
    }

    /**
     * @throws Yosmy\DeniedAuthenticationException
     */
    public function testStartHavingUnresolvableClassificationException()
    {
        $device = 'device';
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';

        $resolveClassification = $this->createMock(Yosmy\Phone\ResolveClassification::class);

        $resolveClassification->expects($this->once())
            ->method('resolve')
            ->with(
                $country,
                $prefix,
                $number
            )
            ->willThrowException(new Yosmy\Phone\UnresolvableClassificationException());

        $analyzePreStartAuthenticationWithCodeToLookupPhone = new Yosmy\AnalyzePreStartAuthenticationWithCodeToLookupPhone(
            $resolveClassification
        );

        $analyzePreStartAuthenticationWithCodeToLookupPhone->analyze(
            $device,
            $country,
            $prefix,
            $number
        );
    }

    /**
     * @throws Yosmy\DeniedAuthenticationException
     */
    public function testStartHavingDeniedAuthenticationException()
    {
        $device = 'device';
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';

        $classification = $this->createMock(Yosmy\Phone\Classification::class);

        $classification->expects($this->once())
            ->method('isVoip')
            ->with()
            ->willReturn(true);

        $resolveClassification = $this->createMock(Yosmy\Phone\ResolveClassification::class);

        $resolveClassification->expects($this->once())
            ->method('resolve')
            ->with(
                $country,
                $prefix,
                $number
            )
            ->willReturn($classification);

        $analyzePreStartAuthenticationWithCodeToLookupPhone = new Yosmy\AnalyzePreStartAuthenticationWithCodeToLookupPhone(
            $resolveClassification
        );

        $this->expectExceptionObject(new Yosmy\DeniedAuthenticationException('El número pertenece a una operadora no permitida'));

        $analyzePreStartAuthenticationWithCodeToLookupPhone->analyze(
            $device,
            $country,
            $prefix,
            $number
        );
    }
}