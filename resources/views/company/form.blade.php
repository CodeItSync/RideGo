<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {!! Form::model($data, ['route' => ['companies.update', $id], 'method' => 'patch']) !!}
        @else
            {!! Form::open(['route' => ['companies.store'], 'method' => 'post']) !!}
        @endif
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }} {{ __('message.information') }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{route('companies.index')}}" class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('name_ar',__('Name'). ' ' .__('Arabic').' <span class="text-danger">*</span>',['class'=>'form-control-label'], false ) }}
                                    {{ Form::text('name_ar',old('name_ar'),['placeholder' => __('Name'). ' ' .__('Arabic'),'class' =>'form-control','required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('name_en',__('Name'). ' ' .__('English').' <span class="text-danger">*</span>',['class'=>'form-control-label'], false ) }}
                                    {{ Form::text('name_en',old('name_en'),['placeholder' => __('Name'). ' ' .__('English'),'class' =>'form-control','required']) }}
                                </div>
                            </div>
                            <hr>
                            {{ Form::submit( __('message.save'), ['class'=>'btn btn-md btn-primary float-right']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</x-master-layout>
