@php
$rqGuildHeader = rq(Ajax\Page\Header\GuildHeader::class);
$rqSectionHeader = rq(Ajax\Page\Header\SectionHeader::class);
@endphp
          <div class="section-header">
            <div @jxnBind($rqGuildHeader)>
              @jxnHtml($rqGuildHeader)
            </div>
            <div class="mt-2">
              <div class="row section-header-title">
                <div class="col" @jxnBind($rqSectionHeader, 'title')>
                </div>
                <div class="col-auto d-flex align-items-end" @jxnBind($rqSectionHeader, 'currency')>
                </div>
              </div>
            </div>
          </div>
