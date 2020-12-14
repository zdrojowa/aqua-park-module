@extends('DashboardModule::dashboard.index')

@section('stylesheets')
    <link rel="stylesheet" href="{{ mix('vendor/css/AquaParkModule.css','') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header clearfix">
                        <h4 class="card-title float-left">Lista karnetów {{ $price->name }}</h4>
                        <a
                            href="{{ route('AquaParkModule::passes.create', ['price' => $price->id]) }}"
                            class="btn btn-success float-right mr-2"
                        >
                            <i class="mdi mdi-plus-circle"></i> Dodaj
                        </a>
                        <a
                            href="{{ route('AquaParkModule::passes.sort', ['price' => $price->id]) }}"
                            class="btn btn-primary float-right mr-2"
                        >
                            <i class="mdi mdi-sort"></i> Sortuj
                        </a>
                        <a
                            href="{{ route('AquaParkModule::prices', ['aquaPark' => $price->aqua_park]) }}"
                            class="btn btn-light float-right mr-2"
                        >
                            <i class="mdi mdi-arrow-left"></i> Do listy cenników
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td>Order</td>
                                    <td>Nazwa</td>
                                    <td>Data utworzenia</td>
                                    <td>Akcje</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($passes as $pass)
                                    <tr>
                                        <td>{{ $pass->order }}</td>
                                        <td>{{ $pass->name }}</td>
                                        <td>{{ $pass->created_at }}</td>
                                        <td>
                                            <div>
                                                <a
                                                    class="btn btn-sm btn-primary"
                                                    href="{{ route('AquaParkModule::passes.edit', [
                                                        'pass' => $pass->id
                                                    ]) }}"
                                                >
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a
                                                    class="btn btn-sm btn-danger remove"
                                                    href="{{ route('AquaParkModule::passes.destroy', [
                                                        'pass' => $pass->id
                                                    ]) }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    @parent
    <script>
        $(document).ready(function(){

            $('a.remove').click(function(e){
                e.preventDefault();
                let url = $(this).attr('href');

                Swal.fire({
                    title: 'Na pewno chcesz to zrobić?',
                    text: 'Nie będzie można tego przywrócić!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d53f3a',
                    confirmButtonText: 'Tak',
                    cancelButtonText: 'Powrót'
                }).then(result => {
                    if(!result.value) return;

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            "_method": "DELETE",
                            "_token": $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function () {
                            Swal.fire('Usunięto!', 'Akcja zakończyła się sukcesem', 'success');
                            location.reload() ;
                        },
                        error: function () {
                            Swal.fire('Wystąpił błąd!', 'Wystąpił błąd po stronie serwera', 'error');
                        }
                    })
                })
            });
        });
    </script>
@endsection
