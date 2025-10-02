<div class="mm-top-navbar">
    <div class="mm-navbar-custom">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="mm-search-bar device-search m-auto">
            </div>
            <div class="d-flex align-items-center">
                <div class="change-mode">
                    <div class="custom-control custom-switch custom-switch-icon custom-control-inline">
                        <div class="custom-switch-inner">
                            <p class="mb-0"> </p>
                            <input type="checkbox" class="custom-control-input" id="dark-mode" data-active="true">
                            <label class="custom-control-label" for="dark-mode" data-mode="toggle">
                                <span class="switch-icon-left">
                                    <svg class="svg-icon" id="h-moon" height="20" width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </span>
                                <span class="switch-icon-right">
                                    <svg class="svg-icon" id="h-sun" height="20" width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-list align-items-center">
                        <!-- Trigger link -->

                        <li class="nav-item nav-icon dropdown">
                            <a href="#" class="search-toggle dropdown-toggle" id="languageDropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @php
                                    $selected_lang_flag = file_exists(public_path('/images/flag/' .app()->getLocale() . '.png')) ? asset('/images/flag/' . app()->getLocale() . '.png') : asset('/images/lang_flag.png');
                                @endphp
                                <img src="{{ $selected_lang_flag }}" class="img-fluid rounded selected-lang" alt="lang-flag">
                                <span class="bg-primary"></span>
                            </a>
                            <div class="mm-sub-dropdown dropdown-menu language-menu" aria-labelledby="languageDropdownMenu">
                                <div class="card shadow-none m-0 border-0">
                                    <div class="p-0 ">
                                        <ul class="dropdown-menu-1 list-group list-group-flush">
                                            @php
                                                $language_option = appSettingData('get')->language_option;
                                                if(!empty($language_option)){
                                                    $language_array = languagesArray($language_option);
                                                }
                                            @endphp
                                            @if(count($language_array) > 0 )
                                                @foreach( $language_array  as $lang )
                                                    <li class="dropdown-item-1 list-group-item px-2">
                                                        <a class="p-0" data-lang="{{ $lang['id'] }}" href="{{ route('change.language',[ 'locale' => $lang['id'] ]) }}">
                                                        @php
                                                            $flag_path = file_exists(public_path('/images/flag/' . $lang['id'] . '.png')) ? asset('/images/flag/' . $lang['id'] . '.png') : asset('/images/lang_flag.png');
                                                        @endphp
                                                            <img src="{{ $flag_path }}" alt="img-flag-{{ $lang['id'] }}" class="img-fluid mr-2 selected-lang-list" />
                                                            {{ $lang['title'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item nav-icon dropdown">
                            <a href="{{route('tracker.logout')}}" class="btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="bg-primary"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="/export" method="GET">
        <div class="modal-header">
          <h5 class="modal-title" id="exportModalLabel">{{__('message.export_data')}}</h5>
          <!--<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>-->
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="fromDate" class="form-label">{{__('message.from')}}:</label>
            <input type="datetime-local" class="form-control" id="fromDate" name="from" required>
          </div>
          <div class="mb-3">
            <label for="toDate" class="form-label">{{__('message.to')}}:</label>
            <input type="datetime-local" class="form-control" id="toDate" name="to" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('message.cancel')}}</button>
          <button type="submit" class="btn btn-primary">{{__('message.export')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>
