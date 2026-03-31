<?php

namespace Siak\Tontine\Service\Report\Pdf;

use Illuminate\Support\Str;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Guild\GuildService;

use function trans;
use function view;

class PdfPrinterService
{
    /**
     * @param GuildService $guildService
     * @param array $config
     */
    public function __construct(private GuildService $guildService, private array $config)
    {}

    /**
     * @param Guild $guild
     * @param string $report
     *
     * @return string
     */
    private function getViewPath(Guild $guild, string $report): string
    {
        $template = $this->guildService->getReportTemplate($guild);
        return "tontine::report.$template.$report";
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
        return PdfGeneratorFacade::getPdf((string)view($templatePath), $config);
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getSessionReportPath(Guild $guild): string
    {
        return $this->getViewPath($guild, 'session');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function getSessionReportFilename(Session $session): string
    {
        return Str::slug(trans('meeting.report.labels.session')) .
            '-' . Str::slug($session->title) . '.pdf';
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getSessionReport(Guild $guild): string
    {
        return $this->getPdf($this->getViewPath($guild, 'session'));
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getSavingsReportPath(Guild $guild): string
    {
        return $this->getViewPath($guild, 'savings');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function getSavingsReportFilename(Session $session): string
    {
        return Str::slug(trans('meeting.report.labels.savings')) .
            '-' . Str::slug($session->title) . '.pdf';
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getSavingsReport(Guild $guild): string
    {
        return $this->getPdf($this->getViewPath($guild, 'savings'));
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getCreditReportPath(Guild $guild): string
    {
        return $this->getViewPath($guild, 'credit');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function getCreditReportFilename(Session $session): string
    {
        return Str::slug(trans('meeting.report.labels.credit')) .
            '-' . Str::slug($session->title) . '.pdf';
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getCreditReport(Guild $guild): string
    {
        return $this->getPdf($this->getViewPath($guild, 'credit'));
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getRoundReportPath(Guild $guild): string
    {
        return $this->getViewPath($guild, 'round');
    }

    /**
     * @param Round $round
     *
     * @return string
     */
    public function getRoundReportFilename(Round $round): string
    {
        return Str::slug(trans('meeting.report.labels.round')) .
            '-' . Str::slug($round->title) . '.pdf';
    }

    /**
     * @param Guild $guild
     *
     * @return string
     */
    public function getRoundReport(Guild $guild): string
    {
        return $this->getPdf($this->getViewPath($guild, 'round'));
    }

    /**
     * @param string $form
     *
     * @return string
     */
    public function getFormViewPath(string $form): string
    {
        return "tontine::entry.raptor.$form";
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
