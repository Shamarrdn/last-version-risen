<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'name.required' => 'الاسم مطلوب',
            'name.string' => 'الاسم يجب أن يكون نصاً',
            'name.max' => 'الاسم يجب أن لا يتجاوز 255 حرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني يجب أن لا يتجاوز 255 حرف',
            'phone.string' => 'رقم الهاتف يجب أن يكون نصاً',
            'phone.max' => 'رقم الهاتف يجب أن لا يتجاوز 20 حرف',
            'subject.string' => 'الموضوع يجب أن يكون نصاً',
            'subject.max' => 'الموضوع يجب أن لا يتجاوز 255 حرف',
            'message.required' => 'الرسالة مطلوبة',
            'message.string' => 'الرسالة يجب أن تكون نصاً',
            'message.max' => 'الرسالة يجب أن لا تتجاوز 2000 حرف',
        ]);

        try {
            Mail::to('ahmeddfathy087@gmail.com')
                ->send(new ContactFormMail(
                    $validated['name'],
                    $validated['email'],
                    $validated['message'],
                    $validated['phone'] ?? null,
                    $validated['subject'] ?? null
                ));

            return redirect()->route('thank-you')->with('success', 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.'])->withInput();
        }
    }
}
