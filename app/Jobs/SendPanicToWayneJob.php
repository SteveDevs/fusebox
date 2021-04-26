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
use App\Models\Panic;
use Illuminate\Support\Facades\Mail;

class SendPanicToWayneJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WAYNE_ENTERPRIZE_TOKEN') . 'kjnkjn',
            'Accept'        => 'application/json',
                ])->post(env('WAYNE_ENTERPRIZE_URL'). 'panic/create', $this->data);
        $response_data = json_decode($response->body());

        if($response->failed()){
            \App\Models\Log::create([
                'message_response' => $response_data->message,
                'action' => 'Send panic to Wayne',
                'level' => 'API_ERROR'
            ]);
            Mail::to('topkin.steve@gmail.com')->send(new SendMail($response_data->message));
        }else{
            \App\Models\Log::create([
                'message_response' => $response_data->message,
                'action' => 'Send panic to Wayne'
            ]);

            Panic::where('id', $this->data['reference_id'])->update(
            [
                'wayne_panic_id' => $response_data->data->panic_id
            ]);
        }

        
    }
}
