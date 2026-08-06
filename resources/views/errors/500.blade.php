@extends('errors.layout')

@section('title', '500 - Gangguan Teknis')
@section('status_code', '500')
@section('status_variant', 'danger')
@section('status_title', 'Terjadi gangguan teknis pada sistem')
@section('status_message', 'Permintaan Anda tidak bisa diselesaikan sekarang karena ada kesalahan internal pada server.')
@section('status_hint', 'Coba ulangi beberapa saat lagi. Jika masalah terus muncul, catat langkah terakhir Anda lalu laporkan ke administrator.')
@section('hero_title', 'Terjadi exception yang butuh penanganan lebih lanjut')
@section('hero_message', 'Saat mode debug dimatikan, pengguna akan melihat halaman yang aman dan rapi tanpa detail stack trace yang sensitif.')
@section('primary_action_label', 'Ke Dashboard')
@section('primary_action_url', auth()->check() ? route('dashboard') : route('login'))

