
<?php
    $auth_user= authSession();
?>
{{ Form::open(['route' => ['rider.destroy', $id], 'method' => 'delete','data--submit'=>'rider'.$id]) }}
<div class="d-flex justify-content-end align-items-center">
    @if($auth_user->can('rider edit'))
    <a class="mr-2" href="{{ route('rider.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('message.rider') ]) }}"><i class="fas fa-edit text-primary"></i></a>
    @endif

    @if($auth_user->can('rider show'))
        <a class="mr-2" href="{{ route('rider.show',$id) }}"><i class="fas fa-eye text-secondary"></i></a>
    @endif

    @if($auth_user->can('rider delete'))
    <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="rider{{$id}}" 
        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.rider') ]) }}"
        title="{{ __('message.delete_form_title',['form'=>  __('message.rider') ]) }}"
        data-message='{{ __("message.delete_msg") }}'>
        <i class="fas fa-trash-alt"></i>
    </a>
    @endif
<a class="mr-2" href="#" data-target="#notifyFormModal" data-toggle="modal"><i class="fas fa-bell text-secondary"></i></a>
</div>
{{ Form::close() }}

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
            <input type="hidden" name="user_type" value="rider" />
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