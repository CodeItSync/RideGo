
<?php
    $auth_user= authSession();
?>
<div class="d-flex justify-content-end align-items-center">
    @if($auth_user->can('driver edit'))
{{ Form::open(['route' => ['driver.destroy', $id], 'method' => 'delete','data--submit'=>'driver'.$id]) }}
    <a class="mr-2" href="{{ route('driver.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('message.driver') ]) }}"><i class="fas fa-edit text-primary"></i></a>
{{ Form::close() }}
    @endif

    @if( $data->status == 'active' && $auth_user->can('driver show') )
{{ Form::open(['route' => ['driver.destroy', $id], 'method' => 'delete','data--submit'=>'driver'.$id]) }}
        <a class="mr-2" href="{{ route('driver.show',$id) }}"><i class="fas fa-eye text-secondary"></i></a>
{{ Form::close() }}
    @endif

    @if($auth_user->can('driver delete'))
{{ Form::open(['route' => ['driver.destroy', $id], 'method' => 'delete','data--submit'=>'driver'.$id]) }}
    <a class="text-danger" href="javascript:void(0)" data--submit="driver{{$id}}"
        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.driver') ]) }}"
        title="{{ __('message.delete_form_title',['form'=>  __('message.driver') ]) }}"
        data-message='{{ __("message.delete_msg") }}'>
        <i class="fas fa-trash-alt"></i>
    </a>
{{ Form::close() }}
    @endif
        {{$id}}
    @if( $data->status == 'active')
        <a class="btn" data-toggle="modal" data-target="#withdrawModal">$</a>

        <!-- Modal -->
        <div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
                <div class="modal-content">
            {!! Form::open(['route' => ['withdraw.admin_request'], 'method' => 'post' ]) !!}
                  <div class="modal-header">
                    <!--<h5 class="modal-title" id="withdrawModalLabel">Modal title</h5>-->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="card" style="border: none">
                        <div class="card-body">
                            <div class="new-user-info">
                                <div class="row" @if(app()->getLocale() == 'ar') style="direction: rtl; text-align: right" @endif >
                                    <input type="hidden" name="user_id" value="{{$id}}"/>
                                    <input type="hidden" name="status" value="1"/>

                                    <!--<div class="form-group">-->
                                        {{ Form::label('amount', __('message.amount').' <span class="text-danger">*</span>',['class'=>'form-control-label'], false ) }}
                                        {{ Form::number('amount', old('amount'), ['class' => 'form-control', 'min' => 0, 'step' => 'any', 'required', 'placeholder' => __('message.amount') ]) }}
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('message.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('message.save') }}</button>
                  </div>
                {!! Form::close() !!}
                    </div>
            </div>
          </div>
        </div>
    @endif


<a href="#" data-target="#notifyFormModal" data-toggle="modal"><i class="fas fa-bell text-secondary"></i></a>
</div>
<!-- Modal -->
<div class="modal fade" id="notifyFormModal" tabindex="-1" role="dialog" aria-labelledby="notifyFormLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notifyFormModal">{{__('message.notify_all')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form action="{{route('users.notify')}}" method="post">
    @csrf
      <div class="modal-body">
          <div class="form-group" style="text-align: {{app()->getLocale() == 'ar'? 'right': 'left' }}">
            <input type="hidden" name="user_type" value="driver" />
            <input type="hidden" name="user_id" value="{{$id}}"/>
            <label for="title" class="col-form-label">{{__('message.title')}}</label>
            <input type="text" name="title" class="form-control" id="title">
            <label for="body" class="col-form-label">{{__('message.body')}}</label>
            <textarea name="body" class="form-control" id="body"></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('message.close')}}</button>
        <button type="submit" class="btn btn-primary">{{__('message.save')}}</button>
      </div>
      </form>
    </div>
  </div>
</div>
