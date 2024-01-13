<?php

namespace Siak\Tontine\Service\Report\Pdf;

use Illuminate\Support\Str;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Tontine\TontineService;

use function strtolower;
use function trans;
use function view;

class PrinterService
{
    /**
     * @param TontineService $tontineService
     * @param array $config
     */
    public function __construct(private TontineService $tontineService, private array $config)
    {}

    /**
     * @param string $report
     *
     * @return string
     */
    private function getViewPath(string $report): string
    {
        $template = $this->tontineService->getReportTemplate();
        return "tontine.report.$template.$report";
    }

    /**
     * @param string $report
     * @param array $config
     *
     * @return string
     */
    private function getPdf(string $report, array $config = []): string
    {
        $templatePath = $this->getViewPath($report);
        $config = [
            ...$this->config,
            ...$config,
            'headerTemplate' => (string)view("$templatePath.tpl.header"),
            'footerTemplate' => (string)view("$templatePath.tpl.footer"),
        ];
        return GeneratorFacade::getPdf((string)view($templatePath), $config);
    }

    /**
     * @return string
     */
    public function getSessionReportPath(): string
    {
        return $this->getViewPath('session');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function getSessionReportFilename(Session $session): string
    {
        return strtolower(trans('meeting.titles.report')) . '-' . Str::slug($session->title) . '.pdf';
    }

    /**
     * @return string
     */
    public function getSessionReport(): string
    {
        return $this->getPdf('session');
    }

    /**
     * @return string
     */
    public function getSavingsReportPath(): string
    {
        return $this->getViewPath('savings');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function getSavingsReportFilename(Session $session): string
    {
        return Str::slug(trans('meeting.titles.savings')) . '-' . Str::slug($session->title) . '.pdf';
    }

    /**
     * @return string
     */
    public function getSavingsReport(): string
    {
        return $this->getPdf('savings');
    }

    /**
     * @return string
     */
    public function getRoundReportPath(): string
    {
        return $this->getViewPath('round');
    }

    /**
     * @param Round $round
     *
     * @return string
     */
    public function getRoundReportFilename(Round $round): string
    {
        return strtolower(trans('meeting.titles.report')) . '-' . Str::slug($round->title) . '.pdf';
    }

    /**
     * @return string
     */
    public function getRoundReport(): string
    {
        return $this->getPdf('round');
    }
}
