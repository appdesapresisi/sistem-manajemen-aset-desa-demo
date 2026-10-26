@extends('errors.layout')

@section('title', '403 — Akses Terbatas')
@section('status_code', '403')
@section('status_title', 'Akses Terbatas! Anda Tidak Memiliki Izin di Area Ini')
@section('status_message', 'Akun Anda saat ini tidak memiliki hak akses atau wewenang yang memadai untuk membuka halaman atau menjalankan tindakan ini.')
@section('primary_action_label', 'Kembali ke Dashboard')
@section('primary_action_url', auth()->check() ? route('dashboard') : route('login'))
@section('secondary_action_label', 'Halaman Sebelumnya')
