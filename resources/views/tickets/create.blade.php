@extends('layouts.nct')

@section('title', __('Submit a ticket') . ' — ' . config('app.name'))

@section('content')
{{-- Stitch "Submit a ticket" design (from stitch_nct_corporate_website, converted to
     English LTR): centered header, single card, drag-and-drop attachments.
     Same field names, route, and validation as before. --}}
@php
    $fieldClass = 'w-full px-4 py-3 border-2 border-gray-300 rounded-lg text-[15px] text-on-surface bg-white focus:border-primary focus:ring-0 transition-all';
    $labelClass = 'text-[12px] font-bold text-on-surface-variant uppercase tracking-wider';
@endphp
<div class="w-full max-w-[800px] mx-auto">

{{-- Header --}}
<header class="text-center mb-12">
<h1 class="text-[32px] leading-[1.2] font-semibold font-headline text-primary mb-2">{{ __('Submit a ticket') }}</h1>
<p class="text-[18px] leading-[1.6] text-on-surface-variant font-light">{{ __("Tell us what's going on and we'll assign a technician to help.") }}</p>
</header>

{{-- Form Card --}}
<div class="bg-white rounded-lg shadow-md border-2 border-gray-300 p-8 md:p-12">
<form method="POST" action="{{ route('my-tickets.store') }}" enctype="multipart/form-data" class="space-y-8">
@csrf

{{-- Title --}}
<div class="flex flex-col gap-2">
<label for="title" class="{{ $labelClass }}">{{ __('Title') }}</label>
<input type="text" id="title" name="title" required maxlength="255" value="{{ old('title') }}"
       placeholder="{{ __('Enter a brief title for the issue') }}" class="{{ $fieldClass }}">
@error('title')<p class="text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Category + Priority --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="flex flex-col gap-2">
<label for="category" class="{{ $labelClass }}">{{ __('Category') }}</label>
<select id="category" name="category" required class="{{ $fieldClass }}">
<option value="" disabled @selected(old('category') === null)>{{ __('Choose a category') }}</option>
<option value="network" @selected(old('category') === 'network')>{{ __('Network') }}</option>
<option value="hardware" @selected(old('category') === 'hardware')>{{ __('Hardware') }}</option>
<option value="software" @selected(old('category') === 'software')>{{ __('Software') }}</option>
<option value="access_request" @selected(old('category') === 'access_request')>{{ __('Access Request') }}</option>
</select>
@error('category')<p class="text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
<label for="priority" class="{{ $labelClass }}">{{ __('Priority') }}</label>
<select id="priority" name="priority" required class="{{ $fieldClass }}">
<option value="" disabled @selected(old('priority') === null)>{{ __('Choose a priority level') }}</option>
<option value="low" @selected(old('priority') === 'low')>{{ __('Low') }}</option>
<option value="medium" @selected(old('priority') === 'medium')>{{ __('Medium') }}</option>
<option value="high" @selected(old('priority') === 'high')>{{ __('High') }}</option>
</select>
@error('priority')<p class="text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>
</div>

{{-- Description --}}
<div class="flex flex-col gap-2">
<label for="description" class="{{ $labelClass }}">{{ __('Description') }}</label>
<textarea id="description" name="description" rows="5" required
          placeholder="{{ __('Please provide enough details about the issue...') }}"
          class="{{ $fieldClass }} resize-none">{{ old('description') }}</textarea>
@error('description')<p class="text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Attachments (drag & drop) --}}
<div class="flex flex-col gap-2">
<label class="{{ $labelClass }}">{{ __('Attachments') }}</label>
<div id="dropzone" class="border-2 border-dashed border-gray-400 rounded-lg p-8 flex flex-col items-center justify-center bg-surface-dim hover:bg-outline-variant/30 transition-colors cursor-pointer group">
<span class="material-symbols-outlined text-4xl text-on-surface-variant/60 mb-3 group-hover:text-primary transition-colors">upload_file</span>
<p class="text-[15px] text-on-surface-variant mb-1">{{ __('Drag files here or click to upload') }}</p>
<p class="text-[12px] text-on-surface-variant/60">{{ __('Up to 5 files, JPG/PNG/PDF, max 5 MB each') }}</p>
<input type="file" id="file-upload" name="attachments[]" class="hidden" multiple accept=".jpg,.jpeg,.png,.pdf">
</div>
<ul id="file-list" class="text-[13px] text-on-surface space-y-1"></ul>
@error('attachments')<p class="text-[12px] text-red-500">{{ $message }}</p>@enderror
@error('attachments.*')<p class="text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Actions --}}
<div class="flex flex-col-reverse md:flex-row items-center justify-end gap-4 pt-6 border-t border-gray-300">
<a href="{{ route('my-tickets.index') }}"
   class="w-full md:w-auto px-8 py-3 rounded-lg text-[15px] font-semibold text-primary border-2 border-gray-300 hover:bg-surface-dim transition-all text-center">{{ __('Cancel') }}</a>
<button type="submit"
        class="w-full md:w-auto px-10 py-3 rounded-lg text-[15px] font-semibold bg-primary text-white hover:bg-primary/90 shadow-sm transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-xl">send</span>
{{ __('Submit ticket') }}
</button>
</div>
</form>
</div>
</div>

<script>
(function () {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-upload');
    const fileList = document.getElementById('file-list');

    function renderFiles() {
        fileList.innerHTML = '';
        Array.from(fileInput.files).forEach(function (f) {
            const li = document.createElement('li');
            li.className = 'flex items-center gap-2';
            const icon = document.createElement('span');
            icon.className = 'material-symbols-outlined text-[16px] text-primary';
            icon.textContent = 'attach_file';
            const name = document.createElement('span');
            name.textContent = f.name + ' (' + (f.size / 1024 / 1024).toFixed(2) + ' MB)';
            li.append(icon, name);
            fileList.append(li);
        });
    }

    dropzone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', renderFiles);

    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropzone.classList.add('border-primary', 'bg-outline-variant/30');
    });
    dropzone.addEventListener('dragleave', function () {
        dropzone.classList.remove('border-primary', 'bg-outline-variant/30');
    });
    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-outline-variant/30');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            renderFiles();
        }
    });
})();
</script>
@endsection
