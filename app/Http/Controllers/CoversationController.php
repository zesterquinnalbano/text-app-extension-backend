<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Conversation;
use App\Message;
use App\TwilioNumber;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Pusher\Pusher;
use Twilio\Rest\Client;

class ConversationController extends Controller
{
    /**
     * Assign Twilio Client
     * @return TwilioClient
     */
    protected $twilioClient = null;

    public function __construct()
    {
        $this->twilioClient = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    public function index()
    {
        $conversation = Conversation::has('messages')
            ->with(['message' => function ($query) {
                $query->orderByDesc('id');
            }, 'contact'])
            ->paginate(15);

        return response()->json([
            'result' => true,
            'data' => $conversation,
        ]);
    }

    /**
     * Send and store message
     *
     */
    public function store(Request $request)
    {
        $validatedInput = $this->validate($request, [
            'conversation_id' => 'sometimes',
            'contact_number_id' => 'required|exists:contacts,id',
            'twilio_number_id' => 'required|exists:twilio_numbers,id',
            'message' => 'required|min:1',
        ]);

        $message = Message::send($this->twilioClient, $validatedInput);

        $messageStatus = null;

        DB::transaction(function () use ($validatedInput, &$messageStatus, $message) {
            $conversation = Conversation::firstOrCreate([
                'id' => $validatedInput['conversation_id'],
            ], [
                'contact_id' => $validatedInput['contact_number_id'],
                'twilio_number_id' => $validatedInput['twilio_number_id'],
            ]);

            $messageStatus = $conversation->message()->create([
                'message' => $validatedInput['message'],
                'sent_by' => Auth::id(),
                'direction' => 'OUTBOUND',
                'status' => 'sent',
                'created_at' => Carbon::now('GMT+08:00'),
            ]);
        }, 3);

        $messageStatus['sent_by'] = Auth::user()->firstname;

        return response()->json([
            'result' => true,
            'data' => $messageStatus,
        ], 200);

    }

    public function sendNew(Request $request)
    {
        $validatedInput = $this->validate($request, [
            'contact_number_ids' => 'required|array|exists:contacts,id',
            'twilio_number_id' => 'required|exists:twilio_numbers,id',
            'message' => 'required|min:1',
        ]);

    }

    /**
     * Get the conversation with messages
     *
     * @param ContactId $id
     * @param Request $request
     *
     * @return response()->json()
     */
    public function show(Request $request, $id)
    {
        $conversation = Conversation::whereContactId($id)->firstOrFail();
        $param = json_decode($request->q);

        $conversation->load(['messages' => function ($query) use ($param) {
            $query->orderByDesc('created_at')->limit($param->limit)->offset($param->offset);
        }, 'contact', 'twilioNumber', 'messages.user:id,firstname']);

        return response()->json([
            'result' => true,
            'data' => $conversation,
        ]);
    }

    /**
     * Delete the conversation
     *
     * @param ConversationId
     * @return Boolean
     */
    public function destroy($id)
    {
        Conversation::find($id)->delete();

        return response()->json([
            'result' => true,
        ]);
    }

    // 'ToCountry' => 'CA',
    // 'ToState' => 'ON',
    // 'SmsMessageSid' => 'SM180ac63e2e76c5d4522cb280627a24ea',
    // 'NumMedia' => '0',
    // 'ToCity' => 'Toronto',
    // 'FromZip' => '',
    // 'SmsSid' => 'SM180ac63e2e76c5d4522cb280627a24ea',
    // 'FromState' => 'ON',
    // 'SmsStatus' => 'received',
    // 'FromCity' => 'TORONTO',
    // 'Body' => 'Hello',
    // 'FromCountry' => 'CA',
    // 'To' => '+16475034763',
    // 'ToZip' => '',
    // 'NumSegments' => '1',
    // 'MessageSid' => 'SM180ac63e2e76c5d4522cb280627a24ea',
    // 'AccountSid' => 'ACca3193a758e9416d172b14c230631bde',
    // 'From' => '+16479311820',
    // 'ApiVersion' => '2010-04-01',

    public static function recieve(Request $request)
    {
        $twilioNumber = TwilioNumber::whereContactNumber($request->To)->firstorFail();
        $contact = Contact::whereContactNumber($request->From)->firstOrFail();
        $messageResponse = null;

        DB::transaction(function () use ($request, $twilioNumber, $contact, &$messageResponse) {
            $conversation = Conversation::firstOrCreate([
                'contact_id' => $contact->id,
                'twilio_number_id' => $twilioNumber->id,
            ]);

            $message = $conversation->message()->create([
                'message' => $request->Body,
                'direction' => 'INBOUND',
                'status' => $request['SmsStatus'],
                'created_at' => Carbon::now('GMT+08:00'),
            ]);

            $messageResponse = $message;
        });

        $pusherOptions = array(
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        );

        $pusher = new Pusher(env('PUSHER_APP_KEY'), env("PUSHER_APP_SECRET"), env("PUSHER_APP_ID"), $pusherOptions);

        $pusher->trigger('inbox-channel', 'new-message-recieved-event', [
            'message' => $messageResponse,
        ]);

        Log::info($messageResponse);

        return response()->json([
            'result' => true,
        ]);
    }
}
