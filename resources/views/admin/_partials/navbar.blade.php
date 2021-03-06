<nav class="navbar navbar-expand-md navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse"
             id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
	        @auth
		        <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('events.index') }}">{{ __('Events') }}</a>
                    </li>
                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"
                           id="navbarDropdown" role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                          Settings
                        </a>
                        <div class="dropdown-menu"
                             aria-labelledby="navbarDropdown">
                            @if(!auth()->user()->is_ldap_user)
		                        <a class="dropdown-item"
		                           href="{{route('users.index')}}">Users</a>
	                        @endif
	                        <a class="dropdown-item"
	                           href="{{route('roles.index')}}">Delegate Roles</a>
                          <a class="dropdown-item"
                             href="{{route('positions.index')}}">Positions</a>
                          <a class="dropdown-item"
                             href="{{route('institutions.index')}}">Institutions</a>
                          <a class="dropdown-item"
                             href="{{route('expense_categories.index')}}">Expenses Categories</a>
                          <a class="dropdown-item"
                             href="{{route('vendors.index')}}">Expenses Vendors</a>
                          <a class="dropdown-item"
                             href="{{route('advertisement_types.index')}}">Advertisement Type</a>
                        </div>
	            </li>
	    </ul>
        @endauth

        <!-- Right Side Of Navbar -->
	        <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
		        @guest
			        <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
			        {{--<li class="nav-item">--}}
			        {{--<a class="nav-link"--}}
			        {{--href="{{ route('register') }}">{{ __('Register') }}</a>--}}
			        {{--</li>--}}
		        @else
			        <li class="nav-item dropdown">
                        <a id="navbarDropdown"
                           class="nav-link dropdown-toggle" href="#"
                           role="button" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false"
                           v-pre>
                            {{ Auth::user()->name }} <span
			                        class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right"
                             aria-labelledby="navbarDropdown">
                            <a class="dropdown-item"
                               href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form"
                                  action="{{ route('logout') }}"
                                  method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
		        @endguest
            </ul>
        </div>
	</div>
</nav>