<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;

class OrderCreated extends Notification
{
  use Queueable;

  protected $order;

  public function __construct(Order $order)
  {
    $this->order = $order;

    try {
      $firebaseService = App::make(FirebaseNotificationService::class);

      $title = "طلب جديد #{$order->order_number}";
      $body = "تم إنشاء طلب جديد بقيمة " . number_format($order->total_amount, 2) . " ريال";
      $link = "/admin/orders/{$order->id}";

      $result = $firebaseService->sendNotificationToAdmins(
        $title,
        $body,
        $link,
        ['link' => $link]
      );
    } catch (\Exception $e) {
    }
  }

  public function via($notifiable): array
  {
    return ['mail', 'database'];
  }

  public function toMail($notifiable): MailMessage
  {
    $this->order->load(['items.product']);

    $orderItems = $this->order->items->map(function($item) {
        return [
            "• {$item->product->name}",
            "  الكمية: {$item->quantity}",
            "  السعر: " . number_format($item->subtotal, 2) . " ريال"
        ];
    })->flatten()->filter()->toArray();

    return (new MailMessage)
        ->view('emails.notifications', [
            'title' => '🛍️ تأكيد الطلب #' . $this->order->order_number,
            'greeting' => "✨ مرحباً {$notifiable->name}",
            'intro' => 'نشكرك على ثقتك! تم استلام طلبك بنجاح.',
            'content' => [
                'sections' => [
                    [
                        'title' => '🛒 تفاصيل المنتجات',
                        'items' => $orderItems
                    ],
                    [
                        'title' => '📍 معلومات التوصيل',
                        'items' => [
                            "العنوان: {$this->order->shipping_address}",
                            "رقم الهاتف: {$this->order->phone}"
                        ]
                    ]
                ],
                'action' => [
                    'text' => '👉 متابعة الطلب',
                    'url' => route('orders.show', $this->order)
                ],
                'outro' => [
                    '🙏 شكراً لتسوقك معنا!',
                    '📞 إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.'
                ]
            ]
        ]);
  }

  public function toArray($notifiable): array
  {
    $data = [
      'title' => 'تأكيد الطلب',
      'message' => 'تم استلام طلبك رقم #' . $this->order->order_number . ' بنجاح',
      'type' => 'order_created',
      'order_number' => $this->order->order_number,
      'total_amount' => $this->order->total_amount
    ];

    return $data;
  }
}
