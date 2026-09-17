<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class ThemedButton extends Component
{
    public string $colorClass;

    public function __construct()
    {
        $role = Auth::user()->role->value ?? 'default';
        switch ($role) {
            case 'admin':
                $this->colorClass = 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
                break;
            case 'loket':
                $this->colorClass = 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500';
                break;
            case 'arsip':
                $this->colorClass = 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
                break;
            case 'seksi1':
                $this->colorClass = 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500';
                break;
            case 'seksi2':
                $this->colorClass = 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500';
                break;
            default:
                $this->colorClass = 'bg-gray-800 hover:bg-gray-700 focus:ring-gray-500';
                break;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.themed-button');
    }
}