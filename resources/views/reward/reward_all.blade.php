
<?php
    $auth_user= authSession();
?>
<div class="d-flex justify-content-end align-items-center">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rewardAllFromModal">{{__('message.reward_all')}}</button>

</div>

<!-- Modal -->
<div class="modal fade" id="rewardAllFromModal" tabindex="-1" role="dialog" aria-labelledby="rewardValueFromLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rewardAllFromModal">{{__('message.reward_all')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form action="{{route('rewards.store')}}" method="post">
    @csrf
      <div class="modal-body">
          <div class="form-group" style="text-align: {{app()->getLocale() == 'ar'? 'right': 'left' }}">
            <label for="reward_value" class="col-form-label">{{__('message.enter_the_reward_value')}}</label>
              <input type="hidden" name="user_type" value="{{request()->has('drivers') ? 'driver' : 'rider'}}" />
                <input type="hidden" id="user-ids" name="user_ids">
            <input type="number" name="amount" step="0.001" class="form-control" id="reward_value">
            <br />
            <select id="user-select" multiple style="width: 100%"></select>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />-->
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const params = new URLSearchParams(window.location.search);
    const userType = params.get('user_type');
    const url = `/rewards/users/search?user_type=${userType ?? 'rider'}`;
  $('#user-select').select2({
    ajax: {
      url: url,
      data: function (params) {
        return { q: params.term, page: params.page || 1 };
      },
      processResults: function (data, params) {
          console.log(data)
        return {
          results: data.items.map(u => ({ id: u.id, text: u.name })),
          pagination: { more: data.pagination.more }
        };
      },
    },
    dataType: 'json',
    closeOnSelect: false,
    placeholder: 'Select users',
    minimumInputLength: 0,
    theme: 'bootstrap',
    minimumResultsForSearch: -1,
  });
  $('#user-select').on('change', function() {
    let selectedIds = $(this).val() || [];
    $('#user-ids').val(selectedIds.join(','));
});
</script>
<style>
    .select2-search {width: 100% !important; display: none !important};
    .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice__remove {backgroud: none !important; border: none !important;
</style>
