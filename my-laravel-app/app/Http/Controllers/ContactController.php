<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    // フォーム表示（EC・ブログ共通）
    public function showForm()
    {
        return view('contact.form');
    }

    //送信処理
    public function submitForm(ContactRequest $request)
    {
        $validated = $request->validated();

        // 今は保存しない（練習用）
        return back()->with('success', 'お問い合わせを送信しました');
    }
}
