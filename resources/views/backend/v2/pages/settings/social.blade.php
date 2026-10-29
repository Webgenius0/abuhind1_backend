@extends('backend.v2.app')

@section('title', 'Social Media Settings')

@section('content')
    <div class="ref-page">
        <!--begin: Page Header-->
        <div class="page-header">
            <div class="page-title">
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <a href="{{ route('admin.social.index') }}" class="breadcrumb-link">Settings</a>
                    <i class="fa-solid fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-active">Social-Media-Links</span>
                </nav>
                <p class="page-description">
                    Manage your social media presence. Add, update, or remove social media profile links displayed on your
                    website.
                </p>
            </div>
        </div>
        <!--end: Page Header-->

        <!--begin: Social Media Card-->
        <div class="card">
            <!--begin: Card Header-->
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                <div
                    class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-share-nodes text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Social Media Links</h3>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Configure your social media profiles and links</p>
                </div>
                <button class="btn-primary text-sm" type="button" onclick="addSocialField()"
                    title="Add a new social media field">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add</span>
                </button>
            </div>
            <!--end: Card Header-->

            <!--begin: Form-->
            <form action="{{ route('admin.social.update') }}" method="POST" class="form" novalidate="novalidate">
                @csrf

                <!--begin: Card Body-->
                <div id="social_media_container">
                    @forelse ($social_link as $link)
                        <div
                            class="social_media grid grid-cols-12 gap-3 mb-4 items-start p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-800">
                            <input type="hidden" name="social_media_id[]" value="{{ $link->id }}">

                            <div class="col-span-3">
                                <select class="form-input @error('social_media.' . $loop->index) is-invalid @enderror"
                                    name="social_media[]" title="Select a social media platform">
                                    <option value="">Select Social</option>
                                    @foreach (['facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'linkedin', 'snapchat', 'pinterest', 'whatsapp', 'telegram'] as $platform)
                                        <option value="{{ $platform }}" @selected($link->social_media == $platform)>
                                            {{ ucfirst($platform === 'youtube' ? 'YouTube' : $platform) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('social_media.' . $loop->index)
                                    <span class="input-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-8">
                                <div class="flex items-center gap-2">
                                    <input type="url"
                                        class="form-input flex-1 @error('profile_link.' . $loop->index) is-invalid @enderror"
                                        name="profile_link[]" value="{{ $link->profile_link }}"
                                        placeholder="Enter the profile link here" title="Enter the profile link here">
                                </div>
                                @error('profile_link.' . $loop->index)
                                    <span class="input-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-1 flex items-center justify-center">
                                <button
                                    class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-all"
                                    type="button" onclick="removeSocialField(this)" data-id="{{ $link->id }}"
                                    data-url="{{ route('admin.social.delete', $link->id) }}"
                                    title="Remove this social media field">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 dark:text-gray-500 py-10" id="empty-message">
                            <i class="fa-solid fa-share-nodes text-4xl mb-3 opacity-50"></i>
                            <p class="text-sm font-medium">No social media links added yet.</p>
                            <p class="text-xs mt-1">Click the "Add" button above to get started.</p>
                        </div>
                    @endforelse
                </div>
                <!--end: Card Body-->

                <!--begin: Form Actions-->
                <div class="form-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn-cancel">
                        <i class="fa-solid fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> <span>Save Changes</span>
                    </button>
                </div>
                <!--end: Form Actions-->
            </form>
            <!--end: Form-->
        </div>
        <!--end: Social Media Card-->
    </div>
@endsection

@push('script')
    <script>
        const MAX_SOCIAL_FIELDS = 10;
        let socialFieldsCount = document.querySelectorAll('#social_media_container .social_media').length;

        const socialPlatformOptions = `
            <option value="">Select Social</option>
            <option value="facebook">Facebook</option>
            <option value="instagram">Instagram</option>
            <option value="twitter">Twitter</option>
            <option value="tiktok">TikTok</option>
            <option value="youtube">YouTube</option>
            <option value="linkedin">LinkedIn</option>
            <option value="snapchat">Snapchat</option>
            <option value="pinterest">Pinterest</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="telegram">Telegram</option>`;

        function addSocialField() {
            const container = document.getElementById('social_media_container');
            const emptyMsg = document.getElementById('empty-message');
            if (emptyMsg) emptyMsg.remove();

            if (socialFieldsCount >= MAX_SOCIAL_FIELDS) {
                window.adminToast(`Maximum ${MAX_SOCIAL_FIELDS} social links fields allowed!`, 'error');
                return;
            }

            const row = document.createElement('div');
            row.className =
                'social_media grid grid-cols-12 gap-3 mb-4 items-start p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-800';
            row.innerHTML = `
                <input type="hidden" name="social_media_id[]" value="">
                <div class="col-span-3">
                    <select class="form-input" name="social_media[]" title="Select a social media platform">
                        ${socialPlatformOptions}
                    </select>
                </div>
                <div class="col-span-8">
                    <input type="url" class="form-input" name="profile_link[]"
                        placeholder="Enter the profile link here" title="Enter the profile link here">
                </div>
                <div class="col-span-1 flex items-center justify-center">
                    <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-all" type="button"
                        onclick="removeSocialField(this)" title="Remove this social media field">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>`;

            container.appendChild(row);
            socialFieldsCount++;
        }

        async function removeSocialField(button) {
            const row = button.closest('.social_media');
            const socialLinkId = button.dataset.id;
            const url = button.dataset.url;

            if (!socialLinkId || !url) {
                row.remove();
                socialFieldsCount--;
                return;
            }

            try {
                const response = await window.adminRequest(url, {
                    method: 'DELETE'
                });
                row.remove();
                socialFieldsCount--;
                window.adminToast(response.message || 'Social media link deleted successfully.');
            } catch (error) {
                window.adminToast(error.message, 'error');
            }
        }
    </script>
@endpush
