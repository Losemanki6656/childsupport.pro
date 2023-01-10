@extends(backpack_view('layouts.top_left'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        __('send sms') => url(config('backpack.base.route_prefix'), 'workers'),
    ];
    
    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <div class="container-fluid mb-3">
        <h2>
            <span class="text-capitalize"> {{ __('Send sms to worker') }} </span>
            {{--            <small id="datatable_info_stack"> {{ __('select your excel file') }} </small> --}}
        </h2>
    </div>
@endsection

@section('after_styles')
    <style>
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.6);
        }

        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px #ddd solid;
            border-top: 4px #2e93e6 solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .is-hide {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div id="loader">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><strong id="fullname"> {{ $worker->fullname }} </strong></div>

                <form id="send-sms" class="form-horizontal" action="{{ route('post-send-sms-to-worker') }}" method="post">
                    <div class="card-body">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="file"> Select Result</label>
                            <div class="col-md-9">
                                <select class="form-control" name="result_id">
                                    @foreach ($results as $item)
                                        <option value="{{$item->id}}"> {{$item->name}} </option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="file">{{ __('Message') }}</label>
                            <div class="col-md-9">
                                <input type="hidden" name="id" value="{{ $id }}">
                                <textarea class="form-control" name="result_comment" id="textarea-input" rows="5"
                                    placeholder="{{ __('type message to worker') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-sm btn-danger" type="reset"><i class="la la-ban"></i> Back</button>
                        <button class="btn btn-sm btn-success" type="submit"><i class="la la-send"></i> Send </button>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection


@section('after_scripts')
    <script>
        $(document).ready(function(e) {

            let status = '{{ isset($status) ? $status : '' }}';
            let message = '{{ isset($message) ? $message : '' }}';

            if (status === 'success') {
                swal({
                    'icon': 'success',
                    'title': '{{ __('Success') }}',
                    'text': message
                });
            }

            if (status === 'error') {
                swal({
                    'icon': 'error',
                    'title': '{{ __('Error') }}',
                    'text': message
                });
            }

            $('#send-sms').submit(function(e) {
                $("#loader").fadeIn(300);
            });
        })
    </script>
@endsection
