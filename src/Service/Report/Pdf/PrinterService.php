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
     * @param string $templatePath
     * @param array $config
     *
     * @return string
     */
    private function getPdf(string $templatePath, array $config = []): string
    {
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
        return $this->getPdf($this->getViewPath('session'));
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
        return $this->getPdf($this->getViewPath('savings'));
    }

    /**
     * @return string
     */
    public function getCreditReportPath(): string
    {
        return $this->getViewPath('credit');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function getCreditReportFilename(Session $session): string
    {
        return Str::slug(trans('meeting.titles.credit')) . '-' . Str::slug($session->title) . '.pdf';
    }

    /**
     * @return string
     */
    public function getCreditReport(): string
    {
        return $this->getPdf($this->getViewPath('credit'));
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
        return Str::slug(trans('meeting.titles.report')) . '-' . Str::slug($round->title) . '.pdf';
    }

    /**
     * @return string
     */
    public function getRoundReport(): string
    {
        return $this->getPdf($this->getViewPath('round'));
    }

    /**
     * @param string $form
     *
     * @return string
     */
    public function getFormViewPath(string $form): string
    {
        return "tontine.entry.raptor.$form";
    }

    /**
     * @param string $form
     *
     * @return string
     */
    public function getEntryForm(string $form): string
    {
        return $this->getPdf($this->getFormViewPath($form));
    }

    /**
     * @param string $form
     *
     * @return string
     */
    public function getFormFilename(string $form): string
    {
        return trans("meeting.entry.files.$form") . '.pdf';
    }
}
