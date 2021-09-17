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
                    @set('value', $item !== null ? $item[$column['name']] : '')
                    @set('type', $id == 'new' && array_key_exists('type-new', $column) ? $column['type-new'] : $column['type'])
                    @set('ext', array_key_exists('ext', $column) ? $column['ext'] : 'jpg')

                    @if($type == 'hidden')
                        <input class="text-input" type="hidden" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                    @elseif($type == 'user')
                        <input class="text-input" type="hidden" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ Auth::id() }}" />
                    @elseif($type != 'display' || $id != 'new')
                        <div class="col-12 col-md-4 col-lg-3">
                            <label for="{{ $column['name'] }}">
                                {{ array_key_exists('title', $column) ? $column['title'] : ucfirst($column['name']) }}

                                @if($column['type'] == 'image')
                                    @if($ext == 'svg')
                                        (SVG)
                                    @endif
                                @elseif($column['type'] == 'file')
                                    ({{ strtoupper($ext) }})
                                @endif
                            </label>
                        </div>

                        <div class="col-12 col-md-8 col-lg-9">
                            @if($type == 'string')
                                <input class="text-input" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" />
                            @elseif($type == 'text')
                                <textarea class="text-input" name="{{ $column['name'] }}" id="{{ $column['name'] }}">{{ $value }}</textarea>
                            @elseif($type == 'currency')
                                <input class="currency-input" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}" autocomplete="off" />
                            @elseif($type == 'date' || $type == 'date-time')
                                <input class="date-picker" type="text" name="{{ $column['name'] }}" id="{{ $column['name'] }}" data-type="{{ $type }}" value="{{ $value == '' ? date('Y-m-d', time()) : preg_replace('/:[0-9][0-9]$/', '', $value) }}" />
                            @elseif($type == 'mkd')
                                <div class="mkd-editor-container">
                                    <textarea class="mkd-editor" name="{{ $column['name'] }}" id="{{ $column['name'] }}" value="{{ $value }}"></textarea>
                                </div>
                            @elseif($type == 'select')
                                <select class="text-input" name="{{ $column['name'] }}" id="{{ $column['name'] }}">
                                    @foreach($column['options'] as $option)
                                        @if(is_array($option))
                                            @set('select_value', $option['value'])
                                            @set('select_title', $option['title'])
                                        @else
                                            @set('select_value', $option)
                                            @set('select_title', $option)
                                        @endif

                                        @if($select_value === $value)
                                            <option value="{{ $select_value }}" selected="selected">{{ $select_title }}</option>
                                        @else
                                            <option value="{{ $select_value }}">{{ $select_title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @elseif($type == 'list')
                                <div class="list" id="{{ $column['name'] }}">
                                    <div class="list-template">
                                        <div class="list-items-row">
                                            <div class="sort-icon" title="Click and drag to reorder">
                                                <div class="sort-icon-inner">
                                                    <div class="sort-icon-inner-bar"></div>
                                                    <div class="sort-icon-inner-bar"></div>
                                                    <div class="sort-icon-inner-bar"></div>
                                                </div>
                                            </div>

                                            @foreach($column['columns'] as $list_column)
                                                <div class="list-items-row-input {{ count($column['columns']) == 1 ? 'wide' : '' }}">
                                                    <input class="list-items-row-input-inner" data-column="{{ $list_column }}" placeholder="{{ $list_column }}" />
                                                </div>
                                            @endforeach

                                            <button class="list-items-row-button" type="button">Delete</button>
                                        </div>
                                    </div>

                                    <div class="list-data">
                                        @if($id != 'new')
                                            @foreach($value as $row)
                                                <div class="list-data-row">
                                                    @foreach($column['columns'] as $list_column)
                                                        <div class="list-data-row-item" data-column="{{ $list_column }}" data-value="{{ $row[$list_column] }}"></div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="list-items"></div>
                                    <button class="list-add-button" type="button">Add</button>
                                </div>
                            @elseif($type == 'image')
                                @set('current_image', "/uploads/$model/img/$id-" . $column['name'] . '.' . $ext)
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
                                <input class="file-upload" type="file" name="{{ $column['name'] }}" id="{{ $column['name'] }}" />

                                @if(file_exists(base_path() . '/public' . $current_file))
                                    <div id="current-file-{{ $column['name'] }}">
                                        <a class="edit-button view" href="{{ $current_file }}?version={{ $item->timestamp() }}" target="_blank">View {{ strtoupper($column['ext']) }}</a>

                                        @if(array_key_exists('delete', $column) && $column['delete'])
                                            <span class="edit-button delete file" data-name="{{ $column['name'] }}">
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
                <div class="col-12">
                    <button id="back" type="button" class="back-button btn btn-secondary">Back</button>
                    <button id="submit" type="button" class="submit-button btn btn-primary no-input">{{ $id == 'new' ? 'Create' : 'Update' }} {{ $heading }} Item</button>
                </div>
            </div>
        </div>
    </form>

    <div id="loading-modal">
        <div class="spinner-container">
            <div class="sk-flow">
                <div class="sk-flow-dot"></div>
                <div class="sk-flow-dot"></div>
                <div class="sk-flow-dot"></div>
            </div>
        </div>
    </div>
@endsection
