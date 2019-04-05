<table class="table table-hover table-responsive-cards"{{ isset($id) ? ' id="' . $id . '"' : '' }}>
    <thead>
    <tr>
        @if(isset($selectable) && $selectable)
            <th class="check"></th>
        @endif
        @foreach($fields as $fieldName => $fieldAccessor)
            <th>{{ $fieldName }}</th>
        @endforeach
        @if(isset($actions) && count($actions))
            <th></th>
        @endif
    </tr>
    </thead>
    <tbody>
        @foreach($data as $rowId => $row)
            <tr>
                @if(isset($selectable) && $selectable)
                    <td class="check">
                        <span class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"
                                   id="{{ (isset($id) ? $id : 'responsiveTable') . '-row' . $rowId }}">
                            <label class="custom-control-label text-hide"
                                   for="{{ (isset($id) ? $id : 'responsiveTable') . '-row' . $rowId }}"></label>
                        </span>
                    </td>
                @endif
                @foreach($fields as $fieldName => $fieldAccessor)
                    <td data-label="{{ $fieldName }}">{{ $fieldAccessor($row) }}</td>
                @endforeach
                @if(isset($actions) && count($actions))
                    <td>
                        @foreach($actions as $actionIcon => $actionAttrGenerator)
                            <a {!! $actionAttrGenerator($row) !!}><i class="fas fa-{{ $actionIcon }}"></i></a>
                        @endforeach
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
