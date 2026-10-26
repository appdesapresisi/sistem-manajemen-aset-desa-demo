@extends('errors.layout')

@section('title', '404 — Halaman Tidak Ditemukan')
@section('status_code', '404')
@section('status_title', 'Waduh! Halaman yang Anda Cari Tidak Ditemukan')
@section('status_message', 'Alamat tautan yang Anda tuju mungkin salah ketik, sudah dipindahkan, atau memang belum terdaftar di dalam sistem inventaris desa.')
@section('primary_action_label', 'Kembali ke Dashboard')
@section('primary_action_url', auth()->check() ? route('dashboard') : route('login'))
@section('secondary_action_label', 'Halaman Sebelumnya')
