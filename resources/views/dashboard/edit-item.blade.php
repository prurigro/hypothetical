@extends('dashboard.core')

@section('dashboard-body')
    @if(!empty($help_text))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="help-text">
                        {!! $help_text !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form id="edit-item" class="edit-item" data-id="{{ $id }}" data-model="{{ $model }}" data-path="{{ isset($path) ? $path : $model }}">
        <input type="hidden" id="token" value="{{ csrf_token() }}" />

        <div class="container-fluid">
            @foreach($columns as $column)
                <div class="row">
                    @set('value', $item[$column['name']])

                    @if($column['type'] == 'hidden')
                        <input class="text-input" type="hidden" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                    @elseif($column['type'] != 'display' || $id != 'new')
                        <div class="col-12 col-md-2">
                            <label for="{{ $column['name'] }}">{{ empty($column['label']) ? ucfirst($column['name']) : $column['label'] }}:</label>
                        </div>

                        <div class="col-12 col-md-10">
                            @if($column['type'] == 'text')
                                <input class="text-input" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                            @elseif($column['type'] == 'date')
                                <input class="date-picker" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value == '' ? date('Y-m-d', time()) : preg_replace('/:[0-9][0-9]$/', '', $value) }}" />
                            @elseif($column['type'] == 'mkd')
                                <textarea class="mkd-editor" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}"></textarea>
                            @elseif($column['type'] == 'select')
                                <select class="text-input" name="{{ $column['name'] }}" id="{{ $column['name'] }}">
                                    @foreach($column['options'] as $option)
                                        @if($option === $value)
                                            <option value="{{ $option }}" selected="selected">{{ $option }}</option>
                                        @else
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @elseif($column['type'] == 'image')
                                <input class="image-upload" type="file" name="{{ $column['name'] }}" id="{{ $column['name'] }}" />

                                @set('current_image', "/uploads/$model/img/$id-" . $column['name'] . '.jpg')

                                @if(file_exists(base_path() . '/public' . $current_image))
                                    <div id="current-image-{{ $column['name'] }}">
                                        <img class="current-image" src="{{ $current_image }}" />

                                        @if(array_key_exists('delete', $column) && $column['delete'])
                                            <span class="edit-button delete image" data-name="{{ $column['name'] }}">
                                                Delete Image
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @elseif($column['type'] == 'file')
                                <input class="file-upload" type="file" name="{{ $column['name'] }}" id="{{ $column['name'] }}" data-ext="{{ $column['ext'] }}" />

                                @set('current_file', "/uploads/$model/files/$id-" . $column['name'] . '.' . $column['ext'])

                                @if(file_exists(base_path() . '/public' . $current_file))
                                    <div id="current-file-{{ $column['name'] }}">
                                        <a class="edit-button view" href="{{ $current_file }}" target="_blank">View {{ strtoupper($column['ext']) }}</a>

                                        @if(array_key_exists('delete', $column) && $column['delete'])
                                            <span class="edit-button delete file" data-name="{{ $column['name'] }}" data-ext="{{ $column['ext'] }}">
                                                Delete {{ strtoupper($column['ext']) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @elseif($column['type'] == 'display')
                                <div class="text-display">{{ $value }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="row">
                <button id="back" type="button" class="back-button btn btn-secondary">Back</button>
                <button id="submit" type="button" class="submit-button btn btn-primary no-input">{{ $id == 'new' ? 'Create' : 'Update' }} {{ $heading }} Item</button>
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
