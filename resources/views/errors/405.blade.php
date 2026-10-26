@extends('errors.layout')

@section('title', '405 — Metode Tidak Diizinkan')
@section('status_code', '405')
@section('status_title', 'Metode Permintaan Tidak Sesuai Spesifikasi Rute')
@section('status_message', 'Aksi HTTP yang dikirimkan tidak diizinkan untuk rute ini. Silakan kembali ke menu navigasi utama untuk melanjutkan alur yang semestinya.')
@section('primary_action_label', 'Kembali ke Dashboard')
@section('primary_action_url', auth()->check() ? route('dashboard') : route('login'))
@section('secondary_action_label', 'Halaman Sebelumnya')
