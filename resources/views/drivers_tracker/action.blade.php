
<?php
    $auth_user= authSession();
?>
{{ Form::open(['route' => ['drivers_tracker.destroy', $id], 'method' => 'delete','data--submit'=>'drivers_tracker'.$id]) }}
<div class="d-flex justify-content-end align-items-center">
    <a class="mr-2" href="{{ route('drivers_tracker.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('Drivers tracker') ]) }}"><i class="fas fa-edit text-primary"></i></a>

    <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="drivers_tracker{{$id}}"
        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('Drivers tracker') ]) }}"
        title="{{ __('message.delete_form_title',['form'=>  __('Drivers tracker') ]) }}"
        data-message='{{ __("message.delete_msg") }}'>
        <i class="fas fa-trash-alt"></i>
    </a>
</div>
{{ Form::close() }}
