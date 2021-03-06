<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct as APIConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct as APIConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct as APIRuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleList;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct as APIRuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct as APIRuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct as APITargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct as APITargetUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\StructBuilder\LayoutResolverStructBuilder;
use Netgen\BlockManager\Core\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\BlockManager\Persistence\TransactionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct;

final class LayoutResolverService extends Service implements APILayoutResolverService
{
    /**
     * @var \Netgen\BlockManager\Core\Validator\LayoutResolverValidator
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Mapper\LayoutResolverMapper
     */
    private $mapper;

    /**
     * @var \Netgen\BlockManager\Core\StructBuilder\LayoutResolverStructBuilder
     */
    private $structBuilder;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $layoutResolverHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    public function __construct(
        TransactionHandlerInterface $transactionHandler,
        LayoutResolverValidator $validator,
        LayoutResolverMapper $mapper,
        LayoutResolverStructBuilder $structBuilder,
        LayoutResolverHandlerInterface $layoutResolverHandler,
        LayoutHandlerInterface $layoutHandler
    ) {
        parent::__construct($transactionHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->layoutResolverHandler = $layoutResolverHandler;
        $this->layoutHandler = $layoutHandler;
    }

    public function loadRule($ruleId): Rule
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    public function loadRuleDraft($ruleId): Rule
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                Value::STATUS_DRAFT
            )
        );
    }

    public function loadRuleArchive($ruleId): Rule
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                Value::STATUS_ARCHIVED
            )
        );
    }

    public function loadRules(?Layout $layout = null, int $offset = 0, ?int $limit = null): RuleList
    {
        if ($layout instanceof Layout && !$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayout = null;
        if ($layout instanceof Layout) {
            $persistenceLayout = $this->layoutHandler->loadLayout(
                $layout->getId(),
                Value::STATUS_PUBLISHED
            );
        }

        $persistenceRules = $this->layoutResolverHandler->loadRules(
            Value::STATUS_PUBLISHED,
            $persistenceLayout,
            $offset,
            $limit
        );

        return new RuleList(
            array_map(
                function (PersistenceRule $rule): Rule {
                    return $this->mapper->mapRule($rule);
                },
                $persistenceRules
            )
        );
    }

    public function getRuleCount(?Layout $layout = null): int
    {
        if ($layout instanceof Layout && !$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = null;
        if ($layout instanceof Layout) {
            $persistenceLayout = $this->layoutHandler->loadLayout(
                $layout->getId(),
                Value::STATUS_PUBLISHED
            );
        }

        return $this->layoutResolverHandler->getRuleCount($persistenceLayout);
    }

    public function matchRules(string $targetType, $targetValue): RuleList
    {
        return new RuleList(
            array_map(
                function (PersistenceRule $rule): Rule {
                    return $this->mapper->mapRule($rule);
                },
                $this->layoutResolverHandler->matchRules($targetType, $targetValue)
            )
        );
    }

    public function loadTarget($targetId): Target
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->layoutResolverHandler->loadTarget(
                $targetId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    public function loadTargetDraft($targetId): Target
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->layoutResolverHandler->loadTarget(
                $targetId,
                Value::STATUS_DRAFT
            )
        );
    }

    public function loadCondition($conditionId): Condition
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->layoutResolverHandler->loadCondition(
                $conditionId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    public function loadConditionDraft($conditionId): Condition
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->layoutResolverHandler->loadCondition(
                $conditionId,
                Value::STATUS_DRAFT
            )
        );
    }

    public function createRule(APIRuleCreateStruct $ruleCreateStruct): Rule
    {
        $this->validator->validateRuleCreateStruct($ruleCreateStruct);

        $createdRule = $this->transaction(
            function () use ($ruleCreateStruct): PersistenceRule {
                return $this->layoutResolverHandler->createRule(
                    RuleCreateStruct::fromArray(
                        [
                            'layoutId' => $ruleCreateStruct->layoutId,
                            'priority' => $ruleCreateStruct->priority,
                            'enabled' => $ruleCreateStruct->enabled,
                            'comment' => $ruleCreateStruct->comment,
                            'status' => Value::STATUS_DRAFT,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($createdRule);
    }

    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be updated.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            function () use ($persistenceRule, $ruleUpdateStruct): PersistenceRule {
                return $this->layoutResolverHandler->updateRule(
                    $persistenceRule,
                    RuleUpdateStruct::fromArray(
                        [
                            'layoutId' => $ruleUpdateStruct->layoutId,
                            'comment' => $ruleUpdateStruct->comment,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function updateRuleMetadata(Rule $rule, APIRuleMetadataUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Metadata can be updated only for published rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        $this->validator->validateRuleMetadataUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            function () use ($persistenceRule, $ruleUpdateStruct): PersistenceRule {
                return $this->layoutResolverHandler->updateRuleMetadata(
                    $persistenceRule,
                    RuleMetadataUpdateStruct::fromArray(
                        [
                            'priority' => $ruleUpdateStruct->priority,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function copyRule(Rule $rule): Rule
    {
        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), $rule->getStatus());

        $copiedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                return $this->layoutResolverHandler->copyRule($persistenceRule);
            }
        );

        return $this->mapper->mapRule($copiedRule);
    }

    public function createDraft(Rule $rule, bool $discardExisting = false): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Drafts can only be created from published rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$discardExisting && $this->layoutResolverHandler->ruleExists($persistenceRule->id, Value::STATUS_DRAFT)) {
            throw new BadStateException('rule', 'The provided rule already has a draft.');
        }

        $ruleDraft = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);

                return $this->layoutResolverHandler->createRuleStatus($persistenceRule, Value::STATUS_DRAFT);
            }
        );

        return $this->mapper->mapRule($ruleDraft);
    }

    public function discardDraft(Rule $rule): void
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be discarded.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->layoutResolverHandler->deleteRule(
                    $persistenceRule->id,
                    Value::STATUS_DRAFT
                );
            }
        );
    }

    public function publishRule(Rule $rule): Rule
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be published.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $publishedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_ARCHIVED);

                if ($this->layoutResolverHandler->ruleExists($persistenceRule->id, Value::STATUS_PUBLISHED)) {
                    $this->layoutResolverHandler->createRuleStatus(
                        $this->layoutResolverHandler->loadRule(
                            $persistenceRule->id,
                            Value::STATUS_PUBLISHED
                        ),
                        Value::STATUS_ARCHIVED
                    );

                    $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_PUBLISHED);
                }

                $publishedRule = $this->layoutResolverHandler->createRuleStatus($persistenceRule, Value::STATUS_PUBLISHED);
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);

                if ($publishedRule->layoutId === null || $this->layoutResolverHandler->getTargetCount($publishedRule) === 0) {
                    $publishedRule = $this->layoutResolverHandler->updateRuleMetadata(
                        $publishedRule,
                        RuleMetadataUpdateStruct::fromArray(
                            [
                                'enabled' => false,
                            ]
                        )
                    );
                }

                return $publishedRule;
            }
        );

        return $this->mapper->mapRule($publishedRule);
    }

    public function restoreFromArchive(Rule $rule): Rule
    {
        if (!$rule->isArchived()) {
            throw new BadStateException('rule', 'Only archived rules can be restored.');
        }

        $archivedRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_ARCHIVED);

        $draftRule = null;
        try {
            $draftRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $draftRule = $this->transaction(
            function () use ($draftRule, $archivedRule): PersistenceRule {
                if ($draftRule instanceof PersistenceRule) {
                    $this->layoutResolverHandler->deleteRule($draftRule->id, $draftRule->status);
                }

                return $this->layoutResolverHandler->createRuleStatus($archivedRule, Value::STATUS_DRAFT);
            }
        );

        return $this->mapper->mapRule($draftRule);
    }

    public function deleteRule(Rule $rule): void
    {
        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), $rule->getStatus());

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->layoutResolverHandler->deleteRule(
                    $persistenceRule->id
                );
            }
        );
    }

    public function enableRule(Rule $rule): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be enabled.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if ($persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already enabled.');
        }

        if ($persistenceRule->layoutId === null) {
            throw new BadStateException('rule', 'Rule is missing a layout and cannot be enabled.');
        }

        if ($this->layoutResolverHandler->getTargetCount($persistenceRule) === 0) {
            throw new BadStateException('rule', 'Rule is missing targets and cannot be enabled.');
        }

        $updatedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                return $this->layoutResolverHandler->updateRuleMetadata(
                    $persistenceRule,
                    RuleMetadataUpdateStruct::fromArray(
                        [
                            'enabled' => true,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function disableRule(Rule $rule): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be disabled.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $updatedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                return $this->layoutResolverHandler->updateRuleMetadata(
                    $persistenceRule,
                    RuleMetadataUpdateStruct::fromArray(
                        [
                            'enabled' => false,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function addTarget(Rule $rule, APITargetCreateStruct $targetCreateStruct): Target
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Targets can be added only to draft rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);
        $ruleTargets = $this->layoutResolverHandler->loadRuleTargets($persistenceRule);

        if (!empty($ruleTargets) && $ruleTargets[0]->type !== $targetCreateStruct->type) {
            throw new BadStateException(
                'rule',
                sprintf(
                    'Rule with ID "%s" only accepts targets with "%s" target type.',
                    $rule->getId(),
                    $ruleTargets[0]->type
                )
            );
        }

        $this->validator->validateTargetCreateStruct($targetCreateStruct);

        $createdTarget = $this->transaction(
            function () use ($persistenceRule, $targetCreateStruct): PersistenceTarget {
                return $this->layoutResolverHandler->addTarget(
                    $persistenceRule,
                    TargetCreateStruct::fromArray(
                        [
                            'type' => $targetCreateStruct->type,
                            'value' => $targetCreateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapTarget($createdTarget);
    }

    public function updateTarget(Target $target, APITargetUpdateStruct $targetUpdateStruct): Target
    {
        if (!$target->isDraft()) {
            throw new BadStateException('target', 'Only draft targets can be updated.');
        }

        $persistenceTarget = $this->layoutResolverHandler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->validator->validateTargetUpdateStruct($target, $targetUpdateStruct);

        $updatedTarget = $this->transaction(
            function () use ($persistenceTarget, $targetUpdateStruct): PersistenceTarget {
                return $this->layoutResolverHandler->updateTarget(
                    $persistenceTarget,
                    TargetUpdateStruct::fromArray(
                        [
                            'value' => $targetUpdateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapTarget($updatedTarget);
    }

    public function deleteTarget(Target $target): void
    {
        if (!$target->isDraft()) {
            throw new BadStateException('target', 'Only draft targets can be deleted.');
        }

        $persistenceTarget = $this->layoutResolverHandler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceTarget): void {
                $this->layoutResolverHandler->deleteTarget($persistenceTarget);
            }
        );
    }

    public function addCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct): Condition
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Conditions can be added only to draft rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $createdCondition = $this->transaction(
            function () use ($persistenceRule, $conditionCreateStruct): PersistenceCondition {
                return $this->layoutResolverHandler->addCondition(
                    $persistenceRule,
                    ConditionCreateStruct::fromArray(
                        [
                            'type' => $conditionCreateStruct->type,
                            'value' => $conditionCreateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapCondition($createdCondition);
    }

    public function updateCondition(Condition $condition, APIConditionUpdateStruct $conditionUpdateStruct): Condition
    {
        if (!$condition->isDraft()) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->layoutResolverHandler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionUpdateStruct($condition, $conditionUpdateStruct);

        $updatedCondition = $this->transaction(
            function () use ($persistenceCondition, $conditionUpdateStruct): PersistenceCondition {
                return $this->layoutResolverHandler->updateCondition(
                    $persistenceCondition,
                    ConditionUpdateStruct::fromArray(
                        [
                            'value' => $conditionUpdateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapCondition($updatedCondition);
    }

    public function deleteCondition(Condition $condition): void
    {
        if (!$condition->isDraft()) {
            throw new BadStateException('condition', 'Only draft conditions can be deleted.');
        }

        $persistenceCondition = $this->layoutResolverHandler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceCondition): void {
                $this->layoutResolverHandler->deleteCondition($persistenceCondition);
            }
        );
    }

    public function newRuleCreateStruct(): APIRuleCreateStruct
    {
        return $this->structBuilder->newRuleCreateStruct();
    }

    public function newRuleUpdateStruct(): APIRuleUpdateStruct
    {
        return $this->structBuilder->newRuleUpdateStruct();
    }

    public function newRuleMetadataUpdateStruct(): APIRuleMetadataUpdateStruct
    {
        return $this->structBuilder->newRuleMetadataUpdateStruct();
    }

    public function newTargetCreateStruct(string $type): APITargetCreateStruct
    {
        return $this->structBuilder->newTargetCreateStruct($type);
    }

    public function newTargetUpdateStruct(): APITargetUpdateStruct
    {
        return $this->structBuilder->newTargetUpdateStruct();
    }

    public function newConditionCreateStruct(string $type): APIConditionCreateStruct
    {
        return $this->structBuilder->newConditionCreateStruct($type);
    }

    public function newConditionUpdateStruct(): APIConditionUpdateStruct
    {
        return $this->structBuilder->newConditionUpdateStruct();
    }
}
