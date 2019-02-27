<?php

namespace App\Helpers;

use App\Contact;
use App\TwilioNumber;
use Twilio\Rest\Client;

class TwilioHelper
{
    /**
     * Assign Twilio Client
     * @return TwilioClient
     */
    protected static $twilioClient;

    public function __construct()
    {
        self::$twilioClient = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    /**
     * Send a message to twilio client
     *
     * @return MessageSID object
     * @param TwilioClient
     * @param Message array
     */
    public static function send($request)
    {
        $twilioNumber = TwilioNumber::findOrFail($request['twilio_number_id']);
        $contact = Contact::findOrFail($request['contact_number_id']);

        $message = self::$twilioClient->messages->create(
            $contact->contact_number,
            [
                'from' => $twilioNumber->contact_number,
                'body' => $request['message'],
                'statusCallback' => env('TWILIO_STATUS_CALLBACK_URL'),
            ]
        );

        return ['status' => $message->status, 'created_at' => $message->dateCreated];
    }

    /**
     * Send a message to many twilio client
     *
     */
    public static function sendMany($request)
    {
        $twilioNumber = TwilioNumber::find($request['twilio_number_id']);
        $contacts = Contact::find($request['contact_number_id']);

        $message = [];

        collect($contacts)->each(function ($contact, $key) use ($request, $twilioNumber, &$message) {
            if ($contact != null) {
                $result = self::$twilioClient->messages->create(
                    $contact->contact_number,
                    [
                        'from' => $twilioNumber->contact_number,
                        'body' => $request['message'],
                        'statusCallback' => env('TWILIO_STATUS_CALLBACK_URL'),
                    ]
                );

                $message['result'][] = ['status' => $result->status, 'contact_ids' => $contact->id, 'created_at' => $result->dateCreated];
                $message['contact_ids'][] = $contact->id;
            }
        });

        return $message;
    }
}
