<?php

namespace App\Twig;

use App\Service\ProcessObjCurrentStateToStringService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class StatutContratExtension extends AbstractExtension
{
    /**
     * @var ProcessObjCurrentStateToStringService
     */
    private $currentStateToStringService;

    public function __construct(ProcessObjCurrentStateToStringService $currentStateToStringService)
    {
        $this->currentStateToStringService = $currentStateToStringService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('statutContrat', [$this, 'doSomething']),
        ];
    }

    public function doSomething($value)
    {
        return ($this->currentStateToStringService)($value);
    }
}
