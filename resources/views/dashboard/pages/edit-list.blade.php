@extends('dashboard.core')

@section('dashboard-heading')
    @if($export && count($rows) > 0)
        <a href="/dashboard/export/{{ $model }}"><button type="button" class="btn btn-secondary">Export</button></a>
    @endif

    @if($create)
        <a href="/dashboard/edit/{{ $model }}/new" class="new-button btn btn-secondary">New</a>
    @endif
@endsection

@section('dashboard-body')
    <div id="edit-list-wrapper">
        <input type="hidden" id="token" value="{{ csrf_token() }}" />

        @if(count($paramdisplay))
            @foreach($paramdisplay as $param)
                <div>Showing {{ $heading }} with a {{ $param['title'] }} of "{{ $param['value'] }}"</div>
            @endforeach
        @endif

        @if($filter)
            @if(!$paginate)
                <input id="filter-input" class="search" placeholder="Filter" />
            @else
                <form
                    id="search-form"
                    class="search-form"
                    data-url="{{ url()->current() }}">

                    <input
                        class="search-form-input search"
                        placeholder="Search"
                        value="{{ request()->query('search') }}"
                    />

                    <input
                        type="submit"
                        class="search-form-submit"
                        value="Search"
                    />
                </form>
            @endif
        @endif

        @if($paginate && $rows->lastPage() !== 1)
            <div class="pagination-navigation-bar">
                <a
                    class="pagination-navigation-bar-arrow prev btn btn-primary {{ $rows->onFirstPage() ? 'btn-disabled' : '' }}"
                    href="/dashboard/edit/{{ $model }}?page={{ $rows->onFirstPage() ? 1 : $rows->currentPage() - 1 }}{{ $query !== '' ? ('&' . $query) : '' }}">

                    Previous Page
                </a>

                <div class="pagination-navigation-bar-page-count">
                    @php
                        $pages_around = 2;
                        $start_page = $rows->currentPage() - $pages_around;
                    @endphp

                    @if($start_page < 1)
                        @php
                            $start_page = 1;
                        @endphp
                    @elseif($start_page + $pages_around > $rows->lastPage())
                        @php
                            $start_page = $rows->lastPage() - $pages_around;
                        @endphp
                    @endif

                    @if($start_page > 1)
                        <a
                            class="pagination-navigation-bar-pages-number btn btn-outline space"
                            href="/dashboard/edit/{{ $model }}?page=1{{ $query !== '' ? ('&' . $query) : '' }}">

                            1
                        </a>
                    @endif

                    @for($page = $start_page; $page < $start_page + 1 + $pages_around * 2; $page++)
                        @if($page === $rows->currentPage())
                            <div class="pagination-navigation-bar-pages-number btn btn-inactive">{{ $page }}</div>
                        @elseif($page <= $rows->lastPage())
                            <a
                                class="pagination-navigation-bar-pages-number btn btn-outline"
                                href="/dashboard/edit/{{ $model }}?page={{ $page }}{{ $query !== '' ? ('&' . $query) : '' }}">

                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($start_page + $pages_around * 2 < $rows->lastPage())
                        <a
                            class="pagination-navigation-bar-pages-number btn btn-outline space"
                            href="/dashboard/edit/{{ $model }}?page={{ $rows->lastPage() }}{{ $query !== '' ? ('&' . $query) : '' }}">

                            {{ $rows->lastPage() }}
                        </a>
                    @endif
                </div>

                <a
                    class="pagination-navigation-bar-arrow next btn btn-primary {{ $rows->hasMorePages() ? '' : 'btn-disabled' }}"
                    href="/dashboard/edit/{{ $model }}?page={{ $rows->hasMorePages() ? $rows->currentPage() + 1 : $rows->currentPage() }}{{ $query !== '' ? ('&' . $query) : '' }}">

                    Next Page
                </a>
            </div>
        @endif

        @if(request()->query('search', null) != null && count($rows) == 0)
            <div class="help-text text-center">No Matching {{ $heading }} Found</div>
        @else
            <ul id="edit-list" class="list-group edit-list list" data-model="{{ $model }}" {{ $sortcol != false ? "data-sort=$sortcol" : '' }}>
                @foreach($rows as $row)
                    <li class="list-group-item {{ $sortcol != false ? 'sortable' : '' }}" data-id="{{ $row['id'] }}">
                        <div class="title-column">
                            @if($sortcol != false)
                                <div class="sort-icon" title="Click and drag to reorder">
                                    <div class="sort-icon-inner">
                                        <div class="sort-icon-inner-bar"></div>
                                        <div class="sort-icon-inner-bar"></div>
                                        <div class="sort-icon-inner-bar"></div>
                                    </div>
                                </div>
                            @endif

                            @foreach($display as $index => $display_column)
                                @if($row[$display_column] != '')
                                    <div class="column">{{ $row[$display_column] }}</div>

                                    @if($index < count($display) - 1)
                                        <div class="spacer">|</div>
                                    @endif
                                @endif
                            @endforeach
                        </div>

                        <div class="button-column">
                            @if(!empty($button))
                                <button type="button" class="action-button btn btn-secondary" data-confirmation="{{ $button[1] }}" data-success="{{ $button[2] }}" data-error="{{ $button[3] }}" data-url="{{ $button[4] }}">{{ $button[0] }}</button>
                            @endif

                            @if(!empty($idlink))
                                <a class="btn btn-secondary" href="{{ $idlink[1] }}{{ $row['id'] }}">{{ $idlink[0] }}</a>
                            @endif

                            <a class="edit-button btn btn-warning" href="/dashboard/edit/{{ $model }}/{{ $row['id'] }}">Edit</a>

                            @if($delete)
                                <button type="button" class="delete-button btn btn-danger">Delete</button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
