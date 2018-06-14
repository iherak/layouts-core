<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided layout name already exists in the system.
 */
final class LayoutNameValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LayoutName) {
            throw new UnexpectedTypeException($constraint, LayoutName::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($this->layoutService->layoutNameExists(trim($value), $constraint->excludedLayoutId)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
