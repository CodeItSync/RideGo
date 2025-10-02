<div id="loading">
    @include('tracker-dashboard.partials._body_loader')
</div>
@include('tracker-dashboard.partials._body_header')
@include('tracker-dashboard.partials._body_sidebar')

<div id="remoteModelData" class="modal fade" role="dialog"></div>
<div class="content-page" style="padding-top: 20px !important;">
    {{ $slot }}
</div>

@include('tracker-dashboard.partials._body_footer')

@include('tracker-dashboard.partials._scripts')
@include('tracker-dashboard.partials._dynamic_script')
