<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Component\Checker;

use Jose\Component\Core\Converter\JsonConverterInterface;

/**
 * Class ClaimCheckerManager.
 */
final class ClaimCheckerManager
{
    /**
     * @var JsonConverterInterface
     */
    private $jsonConverter;

    /**
     * @var ClaimCheckerInterface[]
     */
    private $checkers = [];

    /**
     * ClaimCheckerManager constructor.
     *
     * @param JsonConverterInterface  $jsonConverter
     * @param ClaimCheckerInterface[] $checkers
     */
    private function __construct(JsonConverterInterface $jsonConverter, array $checkers)
    {
        $this->jsonConverter = $jsonConverter;
        foreach ($checkers as $checker) {
            $this->add($checker);
        }
    }

    /**
     * @param JsonConverterInterface  $jsonConverter
     * @param ClaimCheckerInterface[] $checkers
     *
     * @return ClaimCheckerManager
     */
    public static function create(JsonConverterInterface $jsonConverter, array $checkers): ClaimCheckerManager
    {
        return new self($jsonConverter, $checkers);
    }

    /**
     * @param ClaimCheckerInterface $checker
     *
     * @return ClaimCheckerManager
     */
    private function add(ClaimCheckerInterface $checker): ClaimCheckerManager
    {
        $claim = $checker->supportedClaim();
        if (array_key_exists($claim, $this->checkers)) {
            throw new \InvalidArgumentException(sprintf('The claim checker "%s" is already supported.', $claim));
        }

        $this->checkers[$claim] = $checker;

        return $this;
    }

    /**
     * @param array $claims
     *
     * @return array
     */
    public function check(array $claims): array
    {
        foreach ($this->checkers as $claim => $checker) {
            if (array_key_exists($claim, $claims)) {
                $checker->checkClaim($claims[$claim]);
            }
        }

        return $claims;
    }
}
