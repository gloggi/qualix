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
            <th class="actions"></th>
        @endif
    </tr>
    </thead>
    <tbody>
        @foreach($data as $rowId => $row)
            @php
                $rid = (isset($id) ? $id : 'responsiveTable') . '-row' . $rowId;
            @endphp
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
                @if(isset($bild) && $bild)
                    @foreach($bild as $fieldName => $fieldAccessor)
                        <td data-label="{{ $fieldName }}&#xa;">{!! $fieldAccessor($row) !!}</td>
                    @endforeach
                @endif
                @foreach($fields as $fieldName => $fieldAccessor)
                    <td data-label="{{ $fieldName }}&#xa;">{{ $fieldAccessor($row) }}</td>
                @endforeach
                @if(isset($actions) && count($actions))
                    <td class="actions">
                        @foreach($actions as $actionName => $action)
                            @if($actionName === 'delete')
                                <a class="text-danger" data-toggle="modal" href="#delete-{{ $rid }}">
                                    <i class="fas fa-minus-circle"></i>
                                </a>
                                @component('components.delete-modal', array_merge(['id' => 'delete-' . $rid], $action($row)))@endcomponent
                            @else
                                <a href="{{ $action($row) }}"><i class="fas fa-{{ $actionName }}"></i></a>
                            @endif
                        @endforeach
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
