@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="disbursement-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('category', trans('meeting.labels.category'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('category', $categories, $disbursement->category->id, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('member', trans('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('member', $members, $disbursement->member ? $disbursement->member->id : 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('amount', trans('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', $locale->getMoneyValue($disbursement->amount), ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('comment', trans('common.labels.comment'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::text('comment', $disbursement->comment, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
