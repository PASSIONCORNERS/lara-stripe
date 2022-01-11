<?php

namespace App\Jobs\StripeWebhooks;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;

class ChargeSucceededJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Spatie\WebhookClient\Models\WebhookCall */
    public $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        // payload
        $charge = $this->webhookCall->payload['data']['object'];
        $user = User::where('stripe_id', $charge['customer'])->first();


        // do your work here
        if ($user) {
            Payment::create([
                'user_id' => $user->id,
                'subtotal' => $charge['amount'],
                'total' => $charge['amount']
            ]);
        }


        // you can access the payload of the webhook call with `$this->webhookCall->payload`
    }
}
