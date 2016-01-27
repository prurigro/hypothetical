@extends('dashboard.core')

@section('dashboard-body')
    @if(!empty($help_text))
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="help-text">
                        {!! $help_text !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form id="edit-item" class="edit-item" data-id="{{ $id }}" data-model="{{ $model }}">
        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />

        <div class="container-fluid">
            <div class="row">
                @foreach($columns as $column)
                    @set('value', empty($item->$column['name']) ? '' : $item->$column['name'])

                    @if($column['type'] == 'hidden')
                        <input class="text-input" type="hidden" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                    @else
                        <div class="col-xs-12 col-md-2">
                            <label for="{{ $column['name'] }}">{{ empty($column['label']) ? ucfirst($column['name']) : $column['label'] }}:</label>
                        </div>

                        <div class="col-xs-12 col-md-10">
                            @if($column['type'] == 'text')
                                <input class="text-input" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                            @elseif($column['type'] == 'date')
                                <input class="date-time-picker" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ preg_replace('/:[0-9][0-9]$/', '', $value) }}" />
                            @elseif($column['type'] == 'mkd')
                                <textarea class="mkd-editor" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}"></textarea>
                            @endif
                        </div>
                    @endif
                @endforeach

                @if(!empty($imgup) && $imgup)
                    <div class="col-xs-12 col-md-2">
                        <label for="{{ $column['name'] }}">Picture:</label>
                    </div>

                    <div class="col-xs-12 col-md-10">
                        <input class="image-upload" type="file" name="image-upload" id="image-upload" />

                        @set('current_image', "/uploads/$model/$id.jpg")
                        @if(file_exists(base_path() . '/public' . $current_image))
                            <div class="current-image" style="background-image: url({{ $current_image }});" />
                        @else
                            <div>(No Image Set)</div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="row">
                <button id="back" type="button" class="back-button btn btn-default">Back</button>
                <button id="submit" type="button" class="submit-button btn btn-primary">{{ $id == 'new' ? 'Create' : 'Update' }} {{ $heading }} Item</button>
            </div>
        </div>
    </form>

    <div id="loading-modal">
        <div class="spinner-container">
            <div class="sk-folding-cube">
                <div class="sk-cube1 sk-cube"></div>
                <div class="sk-cube2 sk-cube"></div>
                <div class="sk-cube4 sk-cube"></div>
                <div class="sk-cube3 sk-cube"></div>
            </div>
        </div>
    </div>
@endsection
