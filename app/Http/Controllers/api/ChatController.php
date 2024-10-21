<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\ChatResource;
use App\Http\Resources\api\MessageResource;
use App\Models\Message;
use App\Traits\AppResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use AppResponse;


    public $selectedConversation = 0, $message = null, $conversations = [], $messages = [];

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',

            ]
        );

        $user_info = ['email' => $data['email'], 'password' => $data['password']];
        if (Auth::attempt($user_info)) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;
            return $this->success(['token' => $token], 'user logged in successfully');
        }
        return $this->unprocessableVlaue('wrong credentials');
    }

    public function send(Request $request)
    {

        $request->validate([
            'message' => "required",
            'receiver' => "required|exists:users,id",
            "type" => "required|in:text,file"
        ]);

        Message::create([
            'from' => Auth::user()->id,
            'to' => $request->receiver,
            'message' => $request->message,
            'type' => $request->type
        ]);


        return $this->success("message sent");
    }


    public function getChat()
    {
        $authUserId = Auth::id();


        $lastMessages = Message::where(function ($query) use ($authUserId) {
            $query->where('from', $authUserId)  // Sent by the authenticated user
                ->orWhere('to', $authUserId); // Received by the authenticated user
        })
            ->orderBy('created_at', 'desc') // Order by latest messages
            ->get()
            ->unique(function ($message) use ($authUserId) {

                return $message->from === $authUserId ? $message->to : $message->from;
            });

        if ($lastMessages->isNotEmpty()) {
            return $this->success(ChatResource::collection($lastMessages));
        }
        return $this->success([], 'لا يوجد رسائل');
    }


    public function selectConversation($id, Request $request)
    {
        $this->selectedConversation = $id;
        $this->readAt($this->selectedConversation);
        return $this->success(['data' => MessageResource::collection($this->getMessages($request)), 'last_page' => $this->messages->lastPage(), 'total' => $this->messages->total()]);
    }
    public function readAt($receiver)
    {
        $read = Message::where('to', Auth::user()->id)
            ->where('from', $receiver)
            ->whereNull('read_at');
        $read->update(['read_at' => now()]);
        return $this->success([], 'messages read');

    }
    public function getMessages(Request $request)
    {

        if ($this->selectedConversation == 0) {
            return $this->notfound("user not found");
        }

        $authUserId = Auth::id();

        $this->messages = Message::where(function ($query) use ($authUserId) {
            $query->where('from', $authUserId)
                ->where('to', (int) $this->selectedConversation);
        })
            ->orWhere(function ($query) use ($authUserId) {
                $query->where('from', (int) $this->selectedConversation)
                    ->where('to', $authUserId);
            })
            ->orderByDesc('created_at')
            ->paginate(
                $request->perPage ?? 10,
                ['*'],
                'page',
                $request->page ?? 1
            );

        return $this->messages;
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file'
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file')->store('chat', 'public');
        }
        if (!$file) {
            return $this->notFound(
                'file not found'
            );
        }
        return $this->success([
            'data' => $file

        ], 'filed  saved successfully');
    }
}
