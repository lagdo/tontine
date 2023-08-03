<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Category;
use Siak\Tontine\Model\Disbursement;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function trans;
use function trim;

class DisbursementService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * Get a list of members for the dropdown select component.
     *
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->tenantService->tontine()->members()
            ->orderBy('name', 'asc')->pluck('name', 'id')->prepend('', 0);
    }

    /**
     * Find a member.
     *
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(int $memberId): ?Member
    {
        return $this->tenantService->tontine()->members()->find($memberId);
    }

    /**
     * Get the disbursement categories for the dropdown select component.
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        // It is important to call get() before pluck() so the name field is translated.
        return Category::disbursement()->get()->pluck('name', 'id');
    }

    /**
     * Find a disbursement category.
     *
     * @param int $categoryId
     *
     * @return Category|null
     */
    public function getCategory(int $categoryId): ?Category
    {
        return Category::disbursement()->find($categoryId);
    }

    /**
     * Get the disbursements.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getDisbursements(int $page = 0): Collection
    {
        return Disbursement::with(['member', 'category', 'session'])
            ->page($page, $this->tenantService->getLimit())->get();
    }

    /**
     * Get the disbursements for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionDisbursements(Session $session): Collection
    {
        return $session->disbursements()->with(['member', 'category'])->get();
    }

    /**
     * Get a disbursement for a given session.
     *
     * @param Session $session
     * @param int $disbursementId
     *
     * @return Disbursement|null
     */
    public function getSessionDisbursement(Session $session, int $disbursementId): ?Disbursement
    {
        return $session->disbursements()->with(['member', 'category'])->find($disbursementId);
    }

    /**
     * Create a disbursement.
     *
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function createDisbursement(Session $session, array $values): void
    {
        $member = $this->getMember($values['member']);
        $category = $this->getCategory($values['category']);
        if(!$category)
        {
            throw new MessageException(trans('meeting.category.errors.not_found'));
        }

        $disbursement = new Disbursement();
        $disbursement->amount = $values['amount'];
        $disbursement->comment = trim($values['comment']);
        if(($member))
        {
            $disbursement->member()->associate($member);
        }
        $disbursement->category()->associate($category);
        $disbursement->session()->associate($session);
        $disbursement->save();
    }

    /**
     * Update a disbursement.
     *
     * @param Session $session The session
     * @param int $disbursementId
     * @param array $values
     *
     * @return void
     */
    public function updateDisbursement(Session $session, int $disbursementId, array $values): void
    {
        $member = $this->getMember($values['member']);
        $category = $this->getCategory($values['category']);
        if(!$category)
        {
            throw new MessageException(trans('meeting.category.errors.not_found'));
        }
        $disbursement = $session->disbursements()->find($disbursementId);
        if(!$disbursement)
        {
            throw new MessageException(trans('meeting.disbursement.errors.not_found'));
        }

        $disbursement->amount = $values['amount'];
        $disbursement->comment = trim($values['comment']);
        if(($member))
        {
            $disbursement->member()->associate($member);
        }
        else
        {
            $disbursement->member()->dissociate();
        }
        $disbursement->category()->associate($category);
        $disbursement->save();
    }

    /**
     * Delete a disbursement.
     *
     * @param Session $session The session
     * @param int $disbursementId
     *
     * @return void
     */
    public function deleteDisbursement(Session $session, int $disbursementId): void
    {
        $session->disbursements()->where('id', $disbursementId)->delete();
    }
}
