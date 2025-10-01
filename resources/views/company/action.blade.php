
<?php
    $auth_user= authSession();
?>
{{ Form::open(['route' => ['companies.destroy', $id], 'method' => 'delete','data--submit'=>'companies'.$id]) }}
<div class="d-flex justify-content-end align-items-center">
    <a class="mr-2" href="{{ route('companies.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('company') ]) }}"><i class="fas fa-edit text-primary"></i></a>

    <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="companies{{$id}}"
        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('company') ]) }}"
        title="{{ __('message.delete_form_title',['form'=>  __('company') ]) }}"
        data-message='{{ __("message.delete_msg") }}'>
        <i class="fas fa-trash-alt"></i>
    </a>
</div>
{{ Form::close() }}
