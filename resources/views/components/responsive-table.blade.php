<table class="table table-hover table-responsive-cards"{{ isset($id) ? ' id="' . $id . '"' : '' }}>
    <thead>
    <tr>
        @if(isset($selectable) && $selectable)
            <th class="check"></th>
        @endif
        @if(isset($image) && $image)
            @foreach($image as $fieldName => $fieldAccessor)
                <th>{{ $fieldName }}</th>
            @endforeach
        @endif
        @foreach($fields as $fieldName => $fieldAccessor)
            <th>{{ $fieldName }}</th>
        @endforeach
        @if(isset($actions) && count($actions))
            <th class="actions"></th>
        @endif
    </tr>
    </thead>
    <tbody>
        @foreach($data as $rowId => $row)
            @php
                $rid = (isset($id) ? $id : 'responsiveTable') . '-row' . $rowId;
            @endphp
            @if(is_array($row) && isset($row['type']) && $row['type'] === 'header')
                <tr><th colspan="{{ count($fields) + ((isset($selectable) && $selectable) ? 1 : 0) + ((isset($image) && $image) ? count($image) : 0) + ((isset($actions) && count($actions)) ? 1 : 0) }}">{{ $row['text'] }}</th></tr>
            @else
                <tr>
                    @if(isset($selectable) && $selectable)
                        <td class="check">
                            <span class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                       id="{{ $rid }}">
                                <label class="custom-control-label text-hide"
                                       for="{{ $rid }}"></label>
                            </span>
                        </td>
                    @endif
                    @if(isset($image) && $image)
                        @foreach($image as $fieldName => $fieldAccessor)
                            <td data-label="{{ $fieldName }}">{{ $fieldAccessor($row) }}</td>
                        @endforeach
                    @endif
                    @foreach($fields as $fieldName => $fieldAccessor)
                        <td class="{{ $cellClass ?? '' }}" data-label="{{ $fieldName }}">{{ $fieldAccessor($row) }}</td>
                    @endforeach
                    @if(isset($actions) && count($actions))
                        <td class="actions">
                            @foreach($actions as $actionName => $action)
                                @if($actionName === 'delete')
                                    <a class="text-danger" data-toggle="modal" href="#delete-{{ $rid }}" title="{{__('t.global.delete')}}">
                                        <i class="fas fa-minus-circle"></i>
                                    </a>
                                    @component('components.delete-modal', array_merge(['id' => 'delete-' . $rid], $action($row)))@endcomponent
                                @else
                                    <a href="{{ $action($row) }}">
                                        <i class="fas fa-{{ $actionName }}"
                                           @if(Lang::has('t.global.'.$actionName))title="{{__('t.global.'.$actionName)}}" @endif></i>
                                    </a>
                                @endif
                            @endforeach
                        </td>
                    @endif
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
