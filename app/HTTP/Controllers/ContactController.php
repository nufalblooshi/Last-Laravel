<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    function index()
    {
        return view('shop.contact');
    }

    function sendMessageViaEmail(Request $request) {

        $request->validate([
            "name" => "required|max:50",
            "email" => "required|email",
            "subject" => "required|alpha_num|max:50",
            "msg" => "required"
        ]);
        
        $multishop_support_email = "support@multishop.com";

        Mail::to($multishop_support_email)->send(new ContactMail($request->all()));
        return redirect('/contact')->with('success', 'The message sent successfully');
    }
}