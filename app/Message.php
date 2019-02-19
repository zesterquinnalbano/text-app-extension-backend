<?php namespace App;

use App\Contact;
use App\TwilioNumber;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'direction',
        'message',
        'created_at',
        'updated_at',
        'sent_by',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'sent_by');
    }

    public function conversation()
    {
        return $this->belongsTo('App\Conversation', 'conversation_id');
    }

    /**
     * Send a message to twilio client
     *
     * @return MessageSID object
     * @param TwilioClient
     * @param Message array
     */
    public static function send($twilioClient, $request)
    {
        $twilioNumber = TwilioNumber::findOrFail($request['twilio_number_id']);
        $contact = Contact::findOrFail($request['contact_number_id']);

        $message = $twilioClient->messages->create(
            $contact->contact_number,
            [
                'from' => $twilioNumber->contact_number,
                'body' => $request['message'],
            ]
        );

        return ['status' => $message->status];
    }

}
