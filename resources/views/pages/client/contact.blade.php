@extends('layouts.default')
@section('title', 'Contact Us - Options Swift')

@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/terms.css') }}" type="text/css" as="style" />
    <style>
        .contact-wrapper {
            max-width: 600px; /* Thu gọn lại cho vừa vặn vì không còn form */
            margin: 40px auto;
            text-align: center;
        }
        .contact-info {
            margin-top: 30px;
        }
        .contact-info p {
            color: #94a3b8;
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 1.1rem;
        }
        .contact-info strong {
            color: #f8fafc;
        }
        /* Nút bấm Messenger chuẩn màu Facebook */
        .messenger-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: #0084ff; 
            color: #fff !important;
            font-weight: 600;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
            margin-top: 20px;
            font-size: 1.1rem;
        }
        .messenger-btn:hover {
            background-color: #0073e6;
            transform: translateY(-2px);
        }
        .messenger-icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }
    </style>
@endpush

@section('content')
@include('layouts.section.client.header')

<div class="terms-container">
    <div class="glass-card contact-wrapper">
        <div class="terms-header">
            <h1>Contact Us</h1>
            <p>We're here to help. Reach out to our support team anytime.</p>
        </div>

        <div class="contact-info">
            <p><strong>Email Support</strong><br>
                optionsswift85@gmail.com</p>
            
            <p><strong>Business Hours</strong><br>
               Monday - Friday<br>
               9:00 AM - 5:00 PM (EST)</p>
               
            <hr style="border-color: #1e293b; margin: 30px 0;">
               
            <p>The fastest way to reach us is through Facebook Messenger. Click the button below to start a conversation directly with our team.</p>
            
            {{-- <a href="https://m.me/your_page_id" target="_blank" class="messenger-btn">
                <svg class="messenger-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.477 2 2 6.145 2 11.259c0 2.894 1.458 5.485 3.75 7.182.23.17.375.438.384.726l.044 2.113c.018.847 1.002 1.306 1.657.773l2.253-1.836c.216-.176.495-.25.772-.213.993.131 2.02.2 3.09.2 5.523 0 10-4.145 10-9.259S17.523 2 12 2zm1.25 10.366-2.182-2.327c-.244-.26-.649-.315-.951-.129l-3.265 2.016c-.27.167-.532-.143-.33-.377l2.182-2.327c.244-.26.649-.315.951-.129l3.265 2.016c.27.167.532-.143.33-.377z"/>
                </svg>
                Chat on Messenger
            </a> --}}
        </div>
    </div>
</div>

@include('layouts.section.client.footer')
@endsection