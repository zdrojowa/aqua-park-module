@extends('DashboardModule::base')

@section('title','Dashboard')

@section('stylesheets')
    <link rel="stylesheet" href="{{ mix('vendor/css/MediaManager.css','') }}">
    <link rel="stylesheet" href="{{ mix('vendor/css/AquaParkModule.css','') }}">
@endsection

@section('sidebar')
    @include('DashboardModule::sidebar.index', ['menu' => Selene\Support\Facades\MenuRepository::getPresences()])
@endsection

@section('content')
    <div class="content-wrapper">
        <div id="app">
            <b-navbar toggleable="lg" type="dark" variant="dark">
                <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

                <b-collapse id="nav-collapse" is-nav>
                    <b-navbar-nav>
                        <b-nav-item active>Cennik</b-nav-item>
                    </b-navbar-nav>

                    <!-- Right aligned nav items -->
                    <b-navbar-nav class="ml-auto">
                        <b-nav-item href="/dashboard/aqua-parks/{{ $aquaPark->id }}/prices">Lista cenników</b-nav-item>
                    </b-navbar-nav>
                </b-collapse>
            </b-navbar>

            <b-card no-body>
                <b-tabs card>
                    <b-tab active>
                        <template v-slot:title>
                            <b-icon-pencil></b-icon-pencil> Cennik
                        </template>
                        @if (isset($price))
                            <price _id="{{ $price->_id }}">
                                {{ csrf_field() }}
                            </price>
                        @else
                            <price _id="0" park="{{ $aquaPark->id }}">
                                {{ csrf_field() }}
                            </price>
                        @endif
                    </b-tab>
                    @isset($price)
                        <b-tab>
                            <template v-slot:title>
                                <b-icon-images></b-icon-images> Ikonki
                            </template>
                            <icons :id=`{{ $price->_id }}`>
                                {{ csrf_field() }}
                            </icons>
                        </b-tab>
                    @endisset
                </b-tabs>
            </b-card>
        </div>
    </div>
@endsection

@section('javascripts')
    @parent
    @javascript('csrf', csrf_token())
    @javascript('ajaxUpload', route('MediaManager::media.upload.ajax'))
    @javascript('infoUrl', route('MediaManager::media.image.info', ['media' => '%%id%%']))
    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@13.0.1/dist/lazyload.min.js"></script>
    <script src="{{ mix('vendor/js/MediaManager.js') }}"></script>
    <script src="{{ mix('vendor/js/AquaParkModule.js') }}"></script>
@endsection
