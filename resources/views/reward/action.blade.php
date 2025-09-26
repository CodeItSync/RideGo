
<?php
    $auth_user= authSession();
?>
<div class="d-flex justify-content-end align-items-center">
    <a data-toggle="modal" data-target="#rewardValueFromModal-{{$id}}" class="mr-2" href="#" title="{{ __('message.update_form_title',['form' => __('message.rider') ]) }}"><i class="fas fa-award text-primary"></i></a>
</div>

<!-- Modal -->
<div class="modal fade" id="rewardValueFromModal-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="rewardValueFromLabel-{{$id}}" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rewardValueFromModal-{{$id}}">{{__('message.reward_value')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form action="{{route('rewards.store')}}" method="post">
    @csrf
      <div class="modal-body">
          <div class="form-group" style="text-align: {{app()->getLocale() == 'ar'? 'right': 'left' }}">
              <input type="hidden" name="id" value="{{$id}}" />
              <input type="hidden" name="user_type" value="{{request()->has('drivers') ? 'driver' : 'rider'}}" />
            <label for="reward_value" class="col-form-label">{{__('message.enter_the_reward_value')}}</label>
            <input type="number" name="amount" step="0.01" class="form-control" id="reward_value">
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