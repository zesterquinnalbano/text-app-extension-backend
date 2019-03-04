<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Conversation;
use App\Helpers\TwilioHelper;
use App\Message;
use App\TwilioNumber;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Pusher\Pusher;

class ConversationController extends Controller
{
    protected $carbon;

    public function __construct()
    {
        $this->carbon = Carbon::now();
    }

    public function index(Request $request)
    {
        $param = json_decode($request->q);

        $conversation = Conversation::query();

        $conversation->when(isset($param->query), function ($query) use ($param) {
            $query->whereHas('contact', function ($query) use ($param) {
                $query->where('firstname', 'like', "%$param->query%")
                    ->orWhere('lastname', 'like', "%$param->query%");
            });
        });

        $conversation = $conversation->has('messages')
            ->whereHas('contact', function ($query) {
                $query->whereCreatedBy(Auth::id());
            })
            ->with([
                'message' => function ($query) {
                    $query->orderByDesc('id');
                },
                'contact',
            ])
            ->limit($param->limit)->offset($param->offset)
            ->orderBy('updated_at')
            ->get();

        return response()->json([
            'result' => true,
            'data' => $conversation,
        ]);
    }

    /**
     * Send and store message
     *
     */
    public function store(Request $request, TwilioHelper $twilioHelper)
    {
        $validatedInput = $this->validate($request, [
            'conversation_id' => 'sometimes',
            'contact_number_id' => 'required|exists:contacts,id',
            'twilio_number_id' => 'required|exists:twilio_numbers,id',
            'message' => 'required|min:1',
        ]);

        $message = $twilioHelper->send($validatedInput);

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
                'status' => $message['status'],
                'created_at' => $message['created_at'],
            ]);
        }, 3);

        $messageStatus['sent_by'] = Auth::user()->firstname;

        return response()->json([
            'result' => true,
            'data' => $messageStatus,
        ], 200);

    }

    public function sendNew(Request $request, TwilioHelper $twilioHelper)
    {
        $validatedInput = $this->validate($request, [
            'contact_number_id' => 'required|array|exists:contacts,id',
            'twilio_number_id' => 'required|exists:twilio_numbers,id',
            'message' => 'required|min:1',
        ]);

        $message = $twilioHelper->sendMany($validatedInput);

        DB::transaction(function () use ($validatedInput, &$messageStatus, $message) {

            for ($i = 0; $i < count($message['contact_ids']); $i++) {

                $conversation = Conversation::firstOrCreate([
                    'contact_id' => $message['contact_ids'][$i],
                ], [
                    'twilio_number_id' => $validatedInput['twilio_number_id'],
                ]);

                $messageStatus = $conversation->message()->create([
                    'message' => $validatedInput['message'],
                    'sent_by' => Auth::id(),
                    'direction' => 'OUTBOUND',
                    'status' => $message['result'][$i]['status'],
                    'created_at' => $message['result'][$i]['created_at'],
                ]);
            }

        }, 3);

        return response()->json([
            'result' => true,
        ], 200);

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
     * Update new message checker
     *
     * @param Integer $id
     * @return Boolean
     */
    public function update($id)
    {
        $conversation = Conversation::whereContactId($id)->first();

        $conversation->message()->update([
            'new_message' => false,
        ]);

        return response()->json([
            'result' => true,
        ]);
    }

    /**
     * Delete the conversation
     *
     * @param ConversationId
     *
     * @return Boolean
     */
    public function destroy($id)
    {
        Conversation::find($id)->delete();

        return response()->json([
            'result' => true,
        ]);
    }

    public function updateMessageStatus(Request $request)
    {
        DB::transaction(function () use ($request) {
            $conversation = Conversation::where(function ($query) use ($request) {
                $query->whereHas('contact', function ($query) use ($request) {
                    $query->whereContactNumber($request->To);
                })->whereHas('twilioNumber', function ($query) use ($request) {
                    $query->whereContactNumber($request->From);
                });
            })
                ->with('latestMessage')
                ->first();

            if ($conversation) {
                $conversation->latestMessage->update(['status' => $request->MessageStatus]);

                $pusher = new Pusher(env('PUSHER_APP_KEY'), env("PUSHER_APP_SECRET"), env("PUSHER_APP_ID"), ['cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true]);

                $pusher->trigger('message-channel', 'update-message-status-event', [
                    'data' => $conversation->latestMessage,
                ]);

            }
        }, 3);

        return response()->json([], 200);
    }

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
                'created_at' => Carbon::now(),
                'new_message' => true,
            ]);

            $messageResponse = $message;
        });

        $messageResponse['contact_id'] = $contact->id;

        $pusher = new Pusher(env('PUSHER_APP_KEY'), env("PUSHER_APP_SECRET"), env("PUSHER_APP_ID"), ['cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true]);

        $pusher->trigger('inbox-channel', 'new-message-recieved-event', [
            'data' => $messageResponse,
        ]);

        return response()->json([], 200);
    }
}
