<!-- API Helper for Laravel (CSRF + Credentials) -->
<script src="{{ asset('assets/js/api-helper.js') }}"></script>

<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/icon/custom-icon.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/component.js') }}"></script>
<script src="{{ asset('assets/js/theme.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/sima-dropdown.js') }}"></script>
<script src="{{ asset('assets/js/sima-validation.js') }}?v={{ file_exists(public_path('assets/js/sima-validation.js')) ? filemtime(public_path('assets/js/sima-validation.js')) : time() }}"></script>

<div class="floting-button fixed bottom-[50px] right-[30px] z-[1030]">
</div>

@if (!empty($pc_dark_layout))
<script>
  layout_change('{{ $pc_dark_layout }}');
</script>
@endif
@if (!empty($pc_sidebar_theme))
<script>
  layout_theme_sidebar_change('{{ $pc_sidebar_theme }}');
</script>
@endif
@if (!empty($pc_box_container))
<script>
  change_box_container('{{ $pc_box_container }}');
</script>
@endif
@if (!empty($pc_caption_show))
<script>
  layout_caption_change('{{ $pc_caption_show }}');
</script>
@endif
@if (!empty($pc_rtl_layout))
<script>
  layout_rtl_change('{{ $pc_rtl_layout }}');
</script>
@endif
@if (!empty($pc_preset_theme))
<script>
  preset_change('{{ $pc_preset_theme }}');
</script>
@endif
@if (!empty($pc_theme_layout))
<script>
  main_layout_change('{{ $pc_theme_layout }}');
</script>
@endif
