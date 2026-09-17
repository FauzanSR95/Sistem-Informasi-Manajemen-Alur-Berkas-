<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'deadline_arsip' => 'required|integer|min:1',
            'deadline_seksi1' => 'required|integer|min:1',
            'deadline_seksi2' => 'required|integer|min:1',
        ]);

        foreach ($request->all() as $key => $value) {
            if ($key === '_token') continue;
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}