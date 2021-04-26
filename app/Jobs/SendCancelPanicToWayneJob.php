<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SendCancelPanicToWayneJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $panic_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($panic_id)
    {
        $this->panic_id = $panic_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WAYNE_ENTERPRIZE_TOKEN'),
            'Accept'        => 'application/json',
                ])->post(env('WAYNE_ENTERPRIZE_URL'). 'panic/cancel', [ 'panic_id' => $this->panic_id]);
        $response_data = json_decode($response->body());

        if($response->failed()){
            \App\Models\Log::create([
                'message_response' => $response_data->message,
                'action' => 'Send cancel panic to Wayne',
                'level' => 'API_ERROR'
            ]);
            Mail::to('topkin.steve@gmail.com')->send(new SendMail($response_data->message));
        }else{
            \App\Models\Log::create([
                'message_response' => $response_data->message,
                'action' => 'Send cancel panic to Wayne'
            ]);
        }
    }
}
