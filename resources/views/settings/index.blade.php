@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Settings', 'pageDescription' => 'General setting', 'breadcrumbs' => ['Settings' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body">
                <table class="table no-border">
                    <thead>
                    </thead>
                    <tbody>
                    @forelse($settings as $setting)
                        <tr>
                            <td class="vertical-middle" width="20">{{ $loop->index + 1}}.</td>
                            <td class="vertical-middle">
                                {{ $setting->setting_display_name }}
                            </td>
                            <td class="vertical-middle text-right" width="250">
                                {!! \RebelField::type($setting->setting_type, $setting->setting_name, $setting->setting_value, null, ['class' => 'form-control']) !!}
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for setting data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
