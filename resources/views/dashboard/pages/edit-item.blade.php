@extends('dashboard.core')

@section('dashboard-body')
    @if($help_text != '')
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

    <form id="edit-item" class="edit-item" data-id="{{ $id }}" data-model="{{ $model }}">
        <input type="hidden" id="token" value="{{ csrf_token() }}" />

        <div class="container-fluid">
            @foreach($columns as $column)
                <div class="row">
                    @set('value', $item[$column['name']])
                    @set('type', $id == 'new' && array_key_exists('type-new', $column) ? $column['type-new'] : $column['type'])

                    @if($type == 'hidden')
                        <input class="text-input" type="hidden" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                    @elseif($type == 'user')
                        <input class="text-input" type="hidden" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ Auth::id() }}" />
                    @elseif($type != 'display' || $id != 'new')
                        <div class="col-12 col-md-2">
                            <label for="{{ $column['name'] }}">{{ array_key_exists('title', $column) ? $column['title'] : ucfirst($column['name']) }}:</label>
                        </div>

                        <div class="col-12 col-md-10">
                            @if($type == 'text')
                                <input class="text-input" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                            @elseif($type == 'date')
                                <input class="date-picker" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value == '' ? date('Y-m-d', time()) : preg_replace('/:[0-9][0-9]$/', '', $value) }}" />
                            @elseif($type == 'mkd')
                                <div class="mkd-editor-container">
                                    <textarea class="mkd-editor" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}"></textarea>
                                </div>
                            @elseif($type == 'select')
                                <select class="text-input" name="{{ $column['name'] }}" id="{{ $column['name'] }}">
                                    @foreach($column['options'] as $option)
                                        @if($option === $value)
                                            <option value="{{ $option }}" selected="selected">{{ $option }}</option>
                                        @else
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @elseif($type == 'image')
                                @set('current_image', "/uploads/$model/img/$id-" . $column['name'] . '.jpg')
                                <input class="image-upload" type="file" name="{{ $column['name'] }}" id="{{ $column['name'] }}" />

                                @if(file_exists(base_path() . '/public' . $current_image))
                                    <div id="current-image-{{ $column['name'] }}">
                                        <img class="current-image" src="{{ $current_image }}?version={{ $item->timestamp() }}" />

                                        @if(array_key_exists('delete', $column) && $column['delete'])
                                            <span class="edit-button delete image" data-name="{{ $column['name'] }}">
                                                Delete Image
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @elseif($type == 'file')
                                @set('current_file', "/uploads/$model/files/$id-" . $column['name'] . '.' . $column['ext'])
                                <input class="file-upload" type="file" name="{{ $column['name'] }}" id="{{ $column['name'] }}" data-ext="{{ $column['ext'] }}" />

                                @if(file_exists(base_path() . '/public' . $current_file))
                                    <div id="current-file-{{ $column['name'] }}">
                                        <a class="edit-button view" href="{{ $current_file }}?version={{ $item->timestamp() }}" target="_blank">View {{ strtoupper($column['ext']) }}</a>

                                        @if(array_key_exists('delete', $column) && $column['delete'])
                                            <span class="edit-button delete file" data-name="{{ $column['name'] }}" data-ext="{{ $column['ext'] }}">
                                                Delete {{ strtoupper($column['ext']) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @elseif($type == 'display')
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
