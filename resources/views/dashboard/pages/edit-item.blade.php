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
                    @php
                        $value = $item !== null ? $item[$column['name']] : '';
                        $type = $id == 'new' && array_key_exists('type-new', $column) ? $column['type-new'] : $column['type'];
                        $ext = array_key_exists('ext', $column) ? $column['ext'] : $default_img_ext;
                    @endphp

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
                                            @php
                                                $select_value = $option['value'];
                                                $select_title = $option['title'];
                                            @endphp
                                        @else
                                            @php
                                                $select_value = $option;
                                                $select_title = $option;
                                            @endphp
                                        @endif

                                        @if(gettype($select_title) == 'boolean')
                                            @php
                                                $select_title = $select_title ? 'true' : 'false';
                                            @endphp
                                        @endif

                                        @if(gettype($select_value) == 'boolean')
                                            @php
                                                $select_value = $select_value ? 1 : 0;
                                            @endphp
                                        @endif

                                        @if($select_value === $value)
                                            <option value="{{ $select_value }}" selected="selected">{{ $select_title }}</option>
                                        @else
                                            <option value="{{ $select_value }}">{{ $select_title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @elseif($type == 'list')
                                @php
                                    $list_model = App\Dashboard::getModel($value['model']);
                                    $list_columns = $list_model::$dashboard_columns;
                                @endphp

                                <div class="list" id="{{ $column['name'] }}">
                                    <div class="list-template">
                                        <div class="list-items-row" data-id="new">
                                            <div class="sort-icon" title="Click and drag to reorder">
                                                <div class="sort-icon-inner">
                                                    <div class="sort-icon-inner-bar"></div>
                                                    <div class="sort-icon-inner-bar"></div>
                                                    <div class="sort-icon-inner-bar"></div>
                                                </div>
                                            </div>

                                            @foreach($list_columns as $list_column)
                                                <div class="list-items-row-content {{ count($list_columns) == 1 ? 'wide' : '' }}">
                                                    <div class="list-items-row-content-inner" data-column="{{ $list_column['name'] }}" data-type="{{ $list_column['type'] }}">
                                                        @if($list_column['type'] == 'string')
                                                            <input class="list-input" placeholder="{{ $list_column['name'] }}" />
                                                        @elseif($list_column['type'] == 'image')
                                                            <a class="image-link" href="" target="_blank"><img class="image-preview" src="" /></a>
                                                            <input class="list-input image-upload" type="file" data-column="{{ $list_column['name'] }}" data-model="{{ $value['model'] }}" />
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="list-items-row-buttons">
                                                <button class="list-items-row-buttons-delete" type="button">Delete</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="list-data">
                                        @if($id != 'new')
                                            @foreach($value['list'] as $row)
                                                <div class="list-data-row" data-id="{{ $row['id'] }}">
                                                    @foreach($list_columns as $list_column)
                                                        @if($list_column['type'] == 'string')
                                                            @php
                                                                $list_column_value = $row[$list_column['name']]
                                                            @endphp
                                                        @elseif($list_column['type'] == 'image')
                                                            @php
                                                                $list_column_item = $list_model::find($row['id']);
                                                                $list_column_image_ext = array_key_exists('ext', $list_column) ? $list_column['ext'] : $default_img_ext;
                                                                $list_column_image_path = $list_model->getUploadsPath('image') . $row['id'] . '-' . $list_column['name'] . '.' . $list_column_image_ext;
                                                                $list_column_value = file_exists(public_path($list_column_image_path)) ? $list_column_image_path . '?version=' . $list_column_item->timestamp() : '';
                                                            @endphp

                                                            {{ $list_column_image_path }}
                                                        @endif

                                                        <div class="list-data-row-item" data-type="{{ $list_column['type'] }}" data-column="{{ $list_column['name'] }}" data-value="{{ $list_column_value }}"></div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="list-items"></div>
                                    <button class="list-add-button" type="button">Add</button>
                                </div>
                            @elseif($type == 'image')
                                @php
                                    $current_image = "/uploads/$model/img/$id-" . $column['name'] . '.' . $ext;
                                @endphp

                                <div class="upload-wrapper">
                                    <input class="image-upload" type="file" data-column="{{ $column['name'] }}" data-model="{{ $model }}" data-id="{{ $id }}" /> <button type="button" class="clear-upload" title="Clear Upload"></button>
                                </div>

                                @if(file_exists(base_path() . '/public' . $current_image))
                                    <div id="current-image-{{ $column['name'] }}">
                                        <img class="current-image" src="{{ $current_image }}?version={{ $item->timestamp() }}" />

                                        @if(array_key_exists('delete', $column) && $column['delete'])
                                            <span class="edit-button delete image" data-column="{{ $column['name'] }}" data-model="{{ $model }}" data-id="{{ $id }}">
                                                Delete Image
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @elseif($type == 'file')
                                @php
                                    $current_file = "/uploads/$model/files/$id-" . $column['name'] . '.' . $column['ext'];
                                @endphp

                                <div class="upload-wrapper">
                                    <input class="file-upload" type="file" data-column="{{ $column['name'] }}" data-model="{{ $model }}" data-id="{{ $id }}" /> <button type="button" class="clear-upload" title="Clear Upload"></button>
                                </div>

                                @if(file_exists(base_path() . '/public' . $current_file))
                                    <div id="current-file-{{ $column['name'] }}">
                                        <a class="edit-button view" href="{{ $current_file }}?version={{ $item->timestamp() }}" target="_blank">View {{ strtoupper($column['ext']) }}</a>

                                        @if(array_key_exists('delete', $column) && $column['delete'])
                                            <span class="edit-button delete file" data-column="{{ $column['name'] }}" data-model="{{ $model }}" data-id="{{ $id }}">
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
