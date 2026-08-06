@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan')
@section('status_code', '429')
@section('status_variant', 'danger')
@section('status_title', 'Terlalu banyak percobaan dalam waktu singkat')
@section('status_message', 'Sistem menghentikan sementara permintaan Anda untuk mencegah spam, percobaan berulang, atau beban berlebih pada endpoint yang dilindungi.')
@section('status_hint', 'Tunggu sekitar 1 menit, lalu coba lagi. Jika Anda sedang mengirim formulir reset password, cukup kirim satu kali dan cek email atau log aplikasi.')
@section('hero_title', 'Rate limit aktif untuk menjaga stabilitas sistem')
@section('hero_message', 'Proteksi ini dipasang pada endpoint sensitif seperti forgot password agar layanan tidak mudah disalahgunakan oleh request berulang.')
@section('primary_action_label', 'Ke Login')
@section('primary_action_url', route('login'))

