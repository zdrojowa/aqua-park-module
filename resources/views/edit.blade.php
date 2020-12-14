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
            <b-card no-body>
                <b-tabs card>
                    <b-tab active>
                        <template v-slot:title>
                            <b-icon-pencil></b-icon-pencil> Aquapark
                        </template>
                        @isset($aquaPark)
                            <aqua-park :_id=`{{ $aquaPark->_id }}`>
                                {{ csrf_field() }}
                            </aqua-park>
                        @else
                            <aqua-park :_id="0">
                                {{ csrf_field() }}
                            </aqua-park>
                        @endisset
                    </b-tab>
                    @isset($aquaPark)
                        <b-tab>
                            <template v-slot:title>
                                <b-icon-images></b-icon-images> Gallery
                            </template>
                            <gallery :id=`{{ $aquaPark->_id }}`>
                                {{ csrf_field() }}
                            </gallery>
                        </b-tab>
                        <b-tab>
                            <template v-slot:title>
                                <b-icon-map></b-icon-map> Map
                            </template>
                            <map-gallery :id=`{{ $aquaPark->_id }}`>
                                {{ csrf_field() }}
                            </map-gallery>
                        </b-tab>
                        <b-tab>
                            <template v-slot:title>
                                <b-icon-info></b-icon-info> Legenda
                            </template>
                            <legend-info :id=`{{ $aquaPark->_id }}`>
                                {{ csrf_field() }}
                            </legend-info>
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
