<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerDebugBundle\DataCollector;

use Exception;
use Jean85\PrettyVersions;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Twig\Environment;
use Twig\Source;
use Version\Exception\InvalidVersionStringException;
use Version\Version;

final class BlockManagerDataCollector extends DataCollector
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(GlobalVariable $globalVariable, Environment $twig)
    {
        $this->globalVariable = $globalVariable;
        $this->twig = $twig;

        $this->data['version'] = PrettyVersions::getVersion('netgen/layouts-core')->getPrettyVersion();
        $this->data['docs_version'] = 'latest';

        try {
            $version = Version::fromString($this->data['version']);
            $this->data['docs_version'] = sprintf('%d.%d', $version->getMajor(), $version->getMinor());
        } catch (InvalidVersionStringException $e) {
            // Do nothing
        }

        $this->reset();
    }

    public function collect(Request $request, Response $response, Exception $exception = null): void
    {
        $rule = $this->globalVariable->getRule();
        $layoutView = $this->globalVariable->getLayoutView();

        if ($rule instanceof Rule) {
            $this->collectRule($rule);
        }

        if ($layoutView instanceof LayoutViewInterface) {
            $this->collectLayout($layoutView);
        } elseif ($layoutView === false) {
            $this->data['layout'] = false;
        }
    }

    public function reset(): void
    {
        $this->data['rule'] = null;
        $this->data['layout'] = null;
        $this->data['blocks'] = [];
    }

    /**
     * Collects the layout data.
     */
    public function collectLayout(LayoutViewInterface $layoutView): void
    {
        $layout = $layoutView->getLayout();

        $this->data['layout'] = [
            'id' => $layout->getId(),
            'name' => $layout->getName(),
            'type' => $layout->getLayoutType()->getName(),
            'context' => $layoutView->getContext(),
            'template' => null,
            'template_path' => null,
        ];

        if ($layoutView->getTemplate() !== null) {
            $templateSource = $this->getTemplateSource($layoutView->getTemplate());

            $this->data['layout']['template'] = $templateSource->getName();
            $this->data['layout']['template_path'] = $templateSource->getPath();
        }
    }

    /**
     * Collects the rule data.
     */
    public function collectRule(Rule $rule): void
    {
        $this->data['rule'] = [
            'id' => $rule->getId(),
        ];

        foreach ($rule->getTargets() as $target) {
            $this->data['rule']['targets'][] = [
                'type' => $target->getTargetType()::getType(),
                'value' => $this->formatVar($target->getValue()),
            ];
        }

        foreach ($rule->getConditions() as $condition) {
            $this->data['rule']['conditions'][] = [
                'type' => $condition->getConditionType()::getType(),
                'value' => $this->formatVar($condition->getValue()),
            ];
        }
    }

    /**
     * Collects the block view data.
     */
    public function collectBlockView(BlockViewInterface $blockView): void
    {
        $block = $blockView->getBlock();
        $blockDefinition = $block->getDefinition();

        $blockData = [
            'id' => $block->getId(),
            'layout_id' => $block->getLayoutId(),
            'definition' => $blockDefinition->getName(),
            'view_type' => $blockDefinition->hasViewType($block->getViewType()) ?
                $blockDefinition->getViewType($block->getViewType())->getName() :
                'Invalid view type',
            'locale' => $block->getLocale(),
            'template' => null,
            'template_path' => null,
        ];

        if ($blockView->getTemplate() !== null) {
            $templateSource = $this->getTemplateSource($blockView->getTemplate());

            $blockData['template'] = $templateSource->getName();
            $blockData['template_path'] = $templateSource->getPath();
        }

        $this->data['blocks'][] = $blockData;
    }

    /**
     * Returns the collected data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getName(): string
    {
        return 'ngbm';
    }

    private function getTemplateSource(string $name): Source
    {
        return $this->twig->load($name)->getSourceContext();
    }

    /**
     * Formats the variable for safe dumping in the profiler.
     *
     * Used as the BC layer between Symfony 2.8 and Symfony 3.4.
     *
     * @param mixed $var
     *
     * @return mixed
     */
    private function formatVar($var)
    {
        if (method_exists($this, 'varToString')) {
            // @deprecated Remove when support for Symfony 2.8 ends
            return $this->varToString($var);
        }

        return $this->cloneVar($var);
    }
}
