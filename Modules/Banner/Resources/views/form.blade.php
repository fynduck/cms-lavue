@extends('layouts.admin')
@section('title')
    {{ isset($itemTrans[config('app.locale_id')]) ? $itemTrans[config('app.locale_id')]->title : trans('banner::admin.add_banner') }}
@endsection
@section('content')
    <p class="title_page">
        {{ $item ? trans('banner::admin.edit_banner') : trans('banner::admin.add_banner') }}
    </p>
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            @foreach(config('app.locales') as $lang => $language)
                <a class="nav-item nav-link {{ $language->id == config('app.fallback_locale_id') ? 'active' : '' }}"
                   id="nav-{{ $language->id }}-tab"
                   data-toggle="tab" href="#nav-{{ $language->id }}" role="tab" aria-controls="nav-{{ $language->id }}"
                   aria-selected="true">{{ $language->name }}</a>
            @endforeach
        </div>
    </nav>
    <form action="{{ $route }}" enctype="multipart/form-data" method="POST">
        @csrf
        {{ isset($method) ? $method : '' }}
        <button class="btn btn-primary submit_absolute" type="submit">{{ trans('banner::admin.save') }}</button>
        <div class="tab-content pt-4" id="nav-tabContent">
            @foreach(config('app.locales') as $locale => $language)
                <div class="tab-pane fade{{ $locale ==  config('app.fallback_locale_id') ? ' show active' : '' }}"
                     id="nav-{{ $language->id }}"
                     role="tabpanel" aria-labelledby="nav-{{ $language->id }}-tab">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="title_{{ $locale }}">{{ trans('banner::admin.title') }}</label>
                            <input type="text"
                                   class="form-control{{ $errors->first('items.' . $locale . '.title') ? ' is-invalid' : '' }}"
                                   name="items[{{ $locale }}][title]" id="title_{{ $locale }}"
                                   placeholder="{{ trans('banner::admin.title') }}"
                                   value="{{ old('items.' . $locale . '.title', $itemTrans[$locale]->title ?? '') }}">
                        </div>
                        <div class="form-group col-md-4 d-flex align-items-end">
                            <div class="custom-control custom-checkbox my-1 mr-sm-2">
                                <input type="checkbox" name="items[{{ $locale }}][status]" class="custom-control-input"
                                       id="status_{{ $locale }}"
                                       {{ old('items.' . $locale . '.status', $itemTrans[$locale]->status ?? '') ? 'checked' : ''}} value="1">
                                <label class="custom-control-label"
                                       for="status_{{ $locale }}">{{ trans('banner::admin.on_off') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description_{{ $locale }}">{{ trans('banner::admin.description') }}</label>
                        <textarea class="form-control editor" name="items[{{ $locale }}][description]"
                                  id="description_{{ $locale }}"
                                  rows="10">{{ old('items.' . $locale . '.description', $itemTrans[$locale]->description ?? '') }}</textarea>
                    </div>
                </div>
            @endforeach
            <hr>
            <div class="form-row">
                <div class="form-group col">
                    <input type="hidden" name="old_image" class="form-control" value="{{ $item->image ?? '' }}">
                    <upload-image delete_action="{{ route('banners.destroy', $item->id ?? '') }}"
                                  button_text="{{ trans('banner::admin.choose_image') }}"
                                  :old_img="[{{ $old_image ?? '' }}]"></upload-image>
                </div>
                <div class="form-group col">
                    <input type="hidden" name="old_mobile_image" class="form-control" value="{{ $item->mobile_image ?? '' }}">
                    <upload-image delete_action="{{ route('banners.destroy', $item->id ?? '') }}"
                                  button_text="{{ trans('banner::admin.choose_mobile_image') }}"
                                  name="mobile_image"
                                  :old_img="[{{ $old_mobile_image ?? '' }}]"></upload-image>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 form-group">
                    <label for="type">{{ trans('banner::admin.type') }}</label>
                    <select name="type" id="type" class="form-control" required>
                        @if(isset($types) && $types)
                            @foreach($types as $key => $type)
                                <option {{ old('type', $item->type ?? '') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>@lang('admin.show_page')</label>
                        <select-element source="{{ route('admin_live_select.view', ['single' => 'pages']) }}" name="show_page"
                                        :selected="{{ $pagesShow ?? '[]' }}"></select-element>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label>@lang('admin.to_page')</label>
                    <select-element source="{{ route('admin_live_select.view') }}" name="to_page" :multiple="false"
                                    :selected="{{ $toPage ?? '[]' }}"></select-element>
                </div>
                <div class="col-md-4 form-group">
                    <label for="link">{{ trans('banner::admin.link') }}</label>
                    <input type="url" class="form-control{{ $errors->first('link') ? ' is-invalid' : '' }}" name="link" id="link"
                           value="{{ old('sort', $item->link ?? '') }}" placeholder="{{ trans('banner::admin.link') }}">
                </div>
                <div class="col form-group">
                    <label for="target">{{ trans('banner::admin.target') }}</label>
                    <select name="target" id="target" class="form-control" required>
                        @if(isset($target) && $target)
                            @foreach($target as $value)
                                <option {{ ($item && $item->target == $value)? 'selected' : '' }} value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-4 col-md-5 form-group">
                    <label for="sort">{{ trans('banner::admin.sort') }}</label>
                    <input type="number" min="0" class="form-control{{ $errors->first('sort') ? ' is-invalid' : '' }}" name="sort"
                           id="sort" value="{{ old('sort', $item->sort ?? 0) }}" placeholder="{{ trans('banner::admin.sort') }}">
                </div>
                <div class="col form-group">
                    <date-time-from-to :trans="{{ $trans }}"
                                       :old_date="{{ isset($date_range) ? $date_range : '{}' }}"></date-time-from-to>
                </div>
            </div>
            <p class="text-right">
                <button class="btn btn-primary" type="submit">{{ trans('banner::admin.save') }}</button>
            </p>
        </div>
    </form>
@endsection
