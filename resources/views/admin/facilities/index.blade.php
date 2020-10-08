@extends('layouts.admin')
@section('content')
<div class="content">    
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
        	@can('facility_create')
            <a class="btn btn-success" href="{{ route("admin.facilities.create") }}">
                Add Facility
            </a>
            @endcan
            @can('facility_state_government_email')
            <a class="btn btn-success" href="{{ route("admin.facilities.stateEmail") }}">
                State Government Email
            </a>
            @endcan
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Facility List
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Facility">
                            <thead>
                                <tr>
                                    <th width="10"></th>
                                    <th>Sr. No.</th>
                                    <th>Name</th>
                                    <th>Short name</th>
                                    <th>Location</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php $cnt = 1; ?>
                                @foreach($facilities as $key => $facility)
                                    <tr data-entry-id="{{ $facility->id }}">
                                        <td></td>
                                        <td>{{ $cnt ?? '' }}</td>
                                        <td>{{ $facility->name ?? '' }}</td>
                                        <td>{{ $facility->short_name ?? '' }}</td>
                                        <td>{{ $facility->location ?? '' }}</td>
                                        <td>
                                            @can('facility_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.facilities.show', $facility->id) }}">
                                                    View
                                                </a>
                                            @endcan

                                            @can('facility_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.facilities.edit', $facility->id) }}">
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('facility_delete')
                                                <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                                </form>
                                            @endcan

                                        </td>

                                    </tr>
                                    <?php $cnt++; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('facility_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.facilities.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          	headers: {'x-csrf-token': _token},
          	method: 'POST',
          	url: config.url,
          	data: { ids: ids, _method: 'DELETE' }})
          	.done(function () { location.reload() })
      	}
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'asc' ]],
    pageLength: 100,
  });
  	$('.datatable-Facility:not(.ajaxTable)').DataTable({ buttons: dtButtons });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
