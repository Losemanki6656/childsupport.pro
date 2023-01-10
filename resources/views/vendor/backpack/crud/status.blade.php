{{-- regular object attribute --}}
@php
    $value = data_get($entry, $column['name']);
    $id = $entry->id;
    if (is_array($value)) {
        $value = json_encode($value);
    }
@endphp

<span class="status-column" style="float: left" id="status-{{ $id }}">
    @if($value)
      <div class="bg-success" style="border-radius: 50%">
          <i class='nav-icon la la-check'></i>
      </div>
    @else
        <div class="bg-danger" style="border-radius: 50%">
            <i class='nav-icon la la-close'></i>
        </div>
    @endif
</span>

<style>
    .status-column {
        width: 24px;
        margin: 0 auto;
        display: block;
        text-align: center;
    }
</style>